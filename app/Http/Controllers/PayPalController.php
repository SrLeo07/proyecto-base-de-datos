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
                            'currency' => 'GTQ',
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
        // Asegurarnos de que tengamos un token
        if (!$request->token) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'No se proporcionó token de PayPal'], 400);
            }
            return redirect()->route('ordenes')->with('error', 'No se proporcionó token de PayPal');
        }

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        try {
            // Log merchant info before capture
            $merchantInfo = $provider->showOrderDetails($request->token);
            Log::debug('PayPal merchant details', ['order_id' => $request->token, 'merchant_info' => $merchantInfo]);

            $response = $provider->capturePaymentOrder($request->token);

            // Log the raw capture response for debugging
            Log::debug('PayPal capture response', ['request_token' => $request->token, 'response' => $response]);

            if (isset($response['status']) && $response['status'] == 'COMPLETED') {
                $orden = Orden::where('paypal_order_id', $request->token)->first();

                if (! $orden) {
                    Log::error('PayPal success: local order not found for token', ['token' => $request->token]);
                    return redirect()->route('ordenes')->with('error', 'Pago procesado, pero la orden local no fue encontrada.');
                }

                $orden->estado = 'pagado';
                $orden->save();

                // Redirigimos a la página final de "Orden recibida"
                return redirect()->route('ordenes.recibida', $orden->id)
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