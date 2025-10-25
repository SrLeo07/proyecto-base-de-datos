<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalController extends Controller
{
    public function createPayment(Request $request)
    {
        try {
            Log::info('Iniciando creación de pago PayPal', [
                'orden_id' => $request->orden_id,
                'paypal_config' => [
                    'mode' => config('paypal.mode'),
                    'currency' => config('paypal.currency'),
                    'locale' => config('paypal.locale')
                ]
            ]);

            $orden = Orden::findOrFail($request->orden_id);
            
            $provider = new PayPalClient;
            
            // Verificar credenciales antes de usarlas
            $credentials = config('paypal');
            Log::debug('Verificando configuración PayPal', [
                'mode' => $credentials['mode'],
                'has_client_id' => !empty($credentials['sandbox']['client_id']),
                'has_client_secret' => !empty($credentials['sandbox']['client_secret'])
            ]);
            
            $provider->setApiCredentials($credentials);
            
            // Intentar obtener el token de acceso
            try {
                $token = $provider->getAccessToken();
                Log::info('Token de acceso PayPal obtenido correctamente');
            } catch (\Exception $e) {
                Log::error('Error al obtener token de PayPal', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

            $currency = config('paypal.currency', 'USD');

            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => route('paypal.success'),
                    "cancel_url" => route('paypal.cancel'),
                    "brand_name" => "Lavandería Express",
                    "locale" => "es-GT",
                    "landing_page" => "LOGIN",
                    "user_action" => "PAY_NOW",
                ],
                "purchase_units" => [
                    [
                        "reference_id" => "orden_" . $orden->id,
                        "amount" => [
                            "currency_code" => $currency,
                            "value" => number_format($orden->total, 2, '.', ''),
                            "breakdown" => [
                                "item_total" => [
                                    "currency_code" => $currency,
                                    "value" => number_format($orden->total, 2, '.', '')
                                ]
                            ]
                        ],
                        "description" => "Orden #" . $orden->id,
                        "items" => [
                            [
                                "name" => ucfirst($orden->servicio),
                                "description" => "Servicio de " . $orden->servicio,
                                "unit_amount" => [
                                    "currency_code" => $currency,
                                    "value" => number_format($orden->total, 2, '.', '')
                                ],
                                "quantity" => "1",
                                "category" => "DIGITAL_GOODS"
                            ]
                        ]
                    ]
                ]
            ]);

            // Log the full response for debugging
            Log::debug('PayPal createOrder response', ['response' => $response]);

            if (isset($response['id']) && $response['id'] != null) {
                // Guardamos el ID de PayPal en la orden
                $orden->paypal_order_id = $response['id'];
                $orden->save();

                // Encontrar el enlace approve
                $approveLink = null;
                foreach ($response['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        $approveLink = $link['href'];
                        Log::debug('Found PayPal approve link', ['href' => $approveLink]);
                        break;
                    }
                }

                // Si el cliente espera JSON (petición AJAX), devolvemos JSON
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'paypal_order_id' => $response['id'],
                        'approve_url' => $approveLink,
                        'status' => 'success',
                        'orden_id' => $orden->id,
                        'debug_info' => [
                            'order_total' => $orden->total,
                            'currency' => config('paypal.currency'),
                            'mode' => config('paypal.mode')
                        ]
                    ]);
                }

                // Si no es AJAX (por ejemplo un formulario tradicional), redirigimos al enlace de aprobación
                if ($approveLink) {
                    return redirect()->away($approveLink);
                }

                // Si no se obtuvo el enlace, registramos el problema para debugging
                if (isset($response['error'])) {
                    Log::error('PayPal createOrder returned error', ['error' => $response['error']]);
                } else {
                    Log::error('PayPal createOrder: approve link not found', ['response' => $response]);
                }
            }

            return redirect()->back()->with('error', 'Algo salió mal con PayPal');
        } catch (\Exception $e) {
            Log::error('PayPal createOrder exception: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Error al iniciar el pago con PayPal');
        }
    }

    public function success(Request $request)
    {
        Log::debug('PayPal success callback received', [
            'method' => $request->method(),
            'data' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        // El token puede venir de diferentes fuentes
        $token = $request->input('orderID') ?? // Del SDK de PayPal
                $request->input('token') ?? // De la redirección de PayPal
                $request->query('token'); // De la URL en GET
        
        if (!$token) {
            $error = 'No se proporcionó token de PayPal';
            Log::error('PayPal success: no token found', [
                'request_data' => $request->all(),
                'request_method' => $request->method()
            ]);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => true,
                    'message' => $error
                ], 400);
            }
            return redirect()->route('ordenes')->with('error', $error);
        }

        Log::info('Processing PayPal payment', ['token' => $token]);

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        
        try {
            $accessToken = $provider->getAccessToken();
            Log::debug('PayPal access token obtained', ['success' => true]);
        } catch (\Exception $e) {
            Log::error('PayPal access token error', ['error' => $e->getMessage()]);
            throw $e;
        }

        try {
            Log::info('Capturing PayPal payment', ['token' => $token]);
            
            // Verificar el estado de la orden
            $orderDetails = $provider->showOrderDetails($token);
            Log::debug('PayPal order details', [
                'token' => $token,
                'status' => $orderDetails['status'] ?? 'unknown'
            ]);

            // Solo capturar si no está ya capturada
            if (isset($orderDetails['status']) && $orderDetails['status'] !== 'COMPLETED') {
                $response = $provider->capturePaymentOrder($token);
                Log::debug('PayPal capture response', ['response' => $response]);
            } else {
                $response = $orderDetails;
                Log::info('Order already captured', ['status' => $orderDetails['status']]);
            }

            // Log the raw capture response for debugging
            Log::debug('PayPal capture response', ['token' => $token, 'response' => $response]);

            if (isset($response['status']) && $response['status'] == 'COMPLETED') {
                // Buscar primero por paypal_order_id
                $orden = Orden::where('paypal_order_id', $token)->first();

                // Si no se encuentra, intentar buscar por orden_id enviado en la petición
                if (!$orden && $request->has('orden_id')) {
                    $orden = Orden::find($request->input('orden_id'));
                    if ($orden) {
                        $orden->paypal_order_id = $token;
                    }
                }

                if (!$orden) {
                    Log::error('PayPal success: local order not found', [
                        'token' => $token,
                        'orden_id' => $request->input('orden_id')
                    ]);
                    if ($request->wantsJson()) {
                        return response()->json([
                            'error' => true,
                            'message' => 'Orden no encontrada'
                        ], 404);
                    }
                    return redirect()->route('ordenes')
                        ->with('error', 'Pago procesado, pero la orden local no fue encontrada.');
                }

                $orden->estado = 'pagado';
                $orden->save();

                Log::info('Orden marcada como pagada', [
                    'orden_id' => $orden->id,
                    'paypal_order_id' => $token
                ]);

                // Responder según el tipo de petición
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => '¡Pago completado con éxito!',
                        'orden_id' => $orden->id
                    ]);
                }

                // Si no es JSON, redirigir a la vista de la orden
                return redirect()->route('ordenes.show', ['id' => $orden->id])
                    ->with('success', '¡Pago completado con éxito!');
            }

            Log::error('PayPal capture not COMPLETED', ['response' => $response]);
            return redirect()->back()->with('error', 'El pago no pudo ser procesado.');
        } catch (\Exception $e) {
            Log::error('PayPal capture exception: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Error al capturar el pago con PayPal');
        }
    }

    public function cancel()
    {
        return redirect()->back()->with('error', 'El pago fue cancelado.');
    }
}