<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Orden;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

Route::get('/', function () {
    return view('index');
});

Route::get('/orden', function () {
    return view('orden');
});

Route::post('/procesar-orden', function (Request $request) {
    try {
        $tarifas = [
            'lavado' => 3.25,
            'secado' => 2.00,
            'aspirado' => 2.50,
            'lavado_secado' => 4.50
        ];
        $servicio = $request->input('servicio');
        $entrega = $request->input('entrega');
        $total = floatval($tarifas[$servicio] ?? 0);
        if ($entrega === 'entrega_domicilio') {
            $total += 1.25;
        }
        $total = round($total, 2); // Asegurarnos de que solo tenga 2 decimales

        // Preparar datos de la orden
        $orderData = [
            'nombre' => $request->input('nombre'),
            'telefono' => $request->input('telefono'),
            'servicio' => $servicio,
            'entrega' => $entrega,
            'pago' => $request->input('pago'),
            'total' => $total
        ];

        // Añadir dirección solo si es entrega a domicilio
        if ($entrega === 'entrega_domicilio') {
            if (!$request->filled('direccion')) {
                throw new \Exception('La dirección es requerida para entregas a domicilio');
            }
            $orderData['direccion'] = $request->input('direccion');
        }

        // Guardar la orden en la base de datos (tabla 'ordenes')
        $orden = Orden::create($orderData);

        // Si es una petición AJAX o espera JSON
        if ($request->ajax() || $request->wantsJson()) {
            // Render a small summary block that includes the PayPal button
            $html = view('partials.order_summary', ['orden' => $orden])->render();
            return response()->json([
                'orden' => $orden,
                'html' => $html,
            ]);
        }

        // Si no es AJAX, redirigir a la página de la orden
        return redirect()->route('ordenes.show', ['id' => $orden->id]);
        
    } catch (\Exception $e) {
        // Si es una petición AJAX o espera JSON, devolver error en formato JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'error' => true,
                'message' => 'Error al procesar la orden: ' . $e->getMessage()
            ], 500);
        }
        
        // Si no es AJAX, redirigir con mensaje de error
        return redirect()->back()
            ->withInput()
            ->withErrors(['error' => 'Error al procesar la orden: ' . $e->getMessage()]);
    }
});

// Mostrar la orden creada (GET) - usado después del PRG para evitar errores GET/POST
Route::get('/orden/{id}', function ($id) {
    $orden = Orden::findOrFail($id);

    return view('procesar_orden', [
        'nombre' => $orden->nombre,
        'servicio' => $orden->servicio,
        'entrega' => $orden->entrega,
        'telefono' => $orden->telefono,
        'total' => $orden->total,
        'orden' => $orden,
    ]);
})->name('ordenes.show');

Route::get('/ordenes', function () {
    $ordenes = \App\Models\Orden::orderBy('created_at', 'desc')->get();
    return view('ordenes', compact('ordenes'));
});

// Rutas de PayPal
Route::post('/paypal/payment', [App\Http\Controllers\PayPalController::class, 'createPayment'])
    ->name('paypal.payment')
    ->middleware('web');  // Asegura que tengamos acceso a la sesión y CSRF

Route::post('/paypal/success', [App\Http\Controllers\PayPalController::class, 'success'])
    ->name('paypal.success')
    ->middleware('web');

Route::get('/paypal/cancel', [App\Http\Controllers\PayPalController::class, 'cancel'])
    ->name('paypal.cancel')
    ->middleware('web');

// Página que muestra la confirmación final después del pago
Route::get('/orden/{id}/recibida', function ($id) {
    $orden = \App\Models\Orden::findOrFail($id);

    // Sólo permitir ver la página de "recibida" si la orden ya fue pagada
    if ($orden->estado !== 'pagado') {
        return redirect()->route('ordenes.show', ['id' => $orden->id])
                         ->with('error', 'La orden no ha sido pagada todavía. Realiza el pago para confirmar la orden.');
    }

    return view('orden_recibida', ['orden' => $orden]);
})->name('ordenes.recibida');

// Temporary diagnostic route to verify which DB connection Laravel is using
// and which tables/migrations are visible to that connection. Remove this
// route after you've finished debugging.
Route::get('/debug-db', function () {
    $envDb = env('DB_CONNECTION');
    $default = config('database.default');
    $connection = DB::getDefaultConnection();
    $driver = config("database.connections.$connection.driver");
    $username = env('DB_USERNAME');

    try {
        if ($driver === 'oracle') {
            $rows = DB::select("SELECT table_name FROM user_tables");
        } else {
            // Generic fallback for MySQL/Postgres
            $rows = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = ?", [env('DB_DATABASE')]);
        }

        $tables = [];
        foreach ($rows as $r) {
            $arr = (array) $r;
            $tables[] = array_values($arr)[0];
        }

        // Get migrations recorded in the migrations table (if present)
        $migrations = [];
        try {
            $migRows = DB::table('migrations')->orderBy('id', 'desc')->limit(20)->get();
            foreach ($migRows as $m) {
                $migrations[] = $m->migration;
            }
        } catch (\Exception $e) {
            // migrations table might not exist yet
            $migrations = ['error' => $e->getMessage()];
        }

        return response()->json([
            'env_db_connection' => $envDb,
            'config_default' => $default,
            'runtime_connection' => $connection,
            'driver' => $driver,
            'username' => $username,
            'tables_sample' => $tables,
            'migrations_recent' => $migrations,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => true,
            'message' => $e->getMessage(),
            'env_db_connection' => $envDb,
            'config_default' => $default,
            'runtime_connection' => $connection,
            'driver' => $driver,
            'username' => $username,
        ], 500);
    }
});
