<div id="order-summary-{{ $orden->id }}" style="background:#fff;border-radius:10px;padding:16px;border:1px solid #e0e0e0;">
    <h3 style="margin:0 0 8px 0;color:#1565c0;">Revisa tu orden</h3>
    <p style="margin:6px 0"><strong>Nombre:</strong> {{ $orden->nombre }}</p>
    <p style="margin:6px 0"><strong>Servicio:</strong> {{ $orden->servicio }}</p>
    <p style="margin:6px 0"><strong>Entrega:</strong> {{ $orden->entrega }}</p>
    <p style="margin:6px 0"><strong>Teléfono:</strong> {{ $orden->telefono }}</p>
    <p style="margin:8px 0;font-weight:700;color:#1b5e20">Total: Q{{ number_format($orden->total,2) }}</p>

    {{-- Include the PayPal form/button partial --}}
    @include('partials.pay_button', ['orden' => $orden])
</div>
