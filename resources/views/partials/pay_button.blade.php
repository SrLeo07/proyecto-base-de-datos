<div id="pay-form-{{ $orden->id }}">
    <form action="{{ route('paypal.payment') }}" method="POST" class="paypal-inline-form" data-orden-id="{{ $orden->id }}">
        @csrf
        <input type="hidden" name="orden_id" value="{{ $orden->id }}">
        <button type="submit" style="background-color: #0070ba; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin: 10px 0;">
            Pagar con PayPal
        </button>
    </form>
</div>
