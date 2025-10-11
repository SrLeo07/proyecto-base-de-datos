<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Orden;

Route::get('/', function () {
    return view('index');
});

Route::get('/orden', function () {
    return view('orden');
});

Route::post('/procesar-orden', function (Request $request) {
    $tarifas = [
        'lavado' => 25,
        'secado' => 15,
        'aspirado' => 20,
        'lavado_secado' => 35
    ];
    $servicio = $request->input('servicio');
    $entrega = $request->input('entrega');
    $total = $tarifas[$servicio] ?? 0;
    if ($entrega === 'entrega_domicilio') {
        $total += 10;
    }

    // Guardar la orden en la base de datos (tabla 'ordenes')
    $orden = Orden::create([
        'nombre' => $request->input('nombre'),
        'direccion' => $request->input('direccion'),
        'telefono' => $request->input('telefono'),
        'servicio' => $servicio,
        'entrega' => $entrega,
        'pago' => $request->input('pago'),
        'total' => $total,
    ]);

    return view('procesar_orden', [
        'nombre' => $request->input('nombre'),
        'servicio' => $servicio,
        'entrega' => $entrega,
        'telefono' => $request->input('telefono'),
        'total' => $total
    ]);
});

Route::get('/ordenes', function () {
    $ordenes = \App\Models\Orden::orderBy('created_at', 'desc')->get();
    return view('ordenes', compact('ordenes'));
});
