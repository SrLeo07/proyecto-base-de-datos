<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Orden — Lavandería Express</title>
    <script>
        // Verificar la configuración de PayPal (usa el client_id correspondiente al mode configurado)
        console.log('PayPal mode:', '{{ config('paypal.mode') }}', 'Client ID configurado:', '{{ config('paypal.' . config('paypal.mode') . '.client_id') }}');
    </script>
    <script src="https://www.paypal.com/sdk/js?client-id={{ config('paypal.' . config('paypal.mode') . '.client_id') }}&currency={{ config('paypal.currency') }}&locale=es_GT&debug=true" 
            onload="console.log('SDK de PayPal cargado correctamente')"
            onerror="console.error('Error al cargar SDK de PayPal')"></script>
    <style>
        :root{--primary:#0d6efd;--accent:#00a3a3;--muted:#6b7280;--card:#ffffff}
        *{box-sizing:border-box}
        body{font-family:Inter, 'Segoe UI', Arial, sans-serif;background:linear-gradient(180deg,#f3f7fb 0%,#eef9f6 100%);margin:0;padding:28px}
        .wrap{max-width:920px;margin:20px auto;display:flex;gap:24px;align-items:flex-start}
        .card{background:var(--card);border-radius:12px;padding:20px;box-shadow:0 8px 30px rgba(13,110,253,0.06);flex:1}
        .card.small{max-width:380px}
        h1{margin:0 0 8px;color:var(--primary);font-size:1.4rem}
        p.lead{margin:0 0 18px;color:var(--muted)}
        label{display:block;font-weight:600;color:#164e63;margin-top:12px;margin-bottom:6px}
        input,select{width:100%;padding:10px;border-radius:8px;border:1px solid #dbeafe;background:#f8fbff}
        .row{display:flex;gap:12px}
        .row .col{flex:1}
        .badge{display:inline-block;background:#e6f7ff;color:#055160;padding:6px 10px;border-radius:999px;font-weight:700}
        .total{font-size:1.25rem;font-weight:800;color:#0f172a}
        .muted{color:var(--muted);font-size:0.95rem}
        .btn-primary{display:inline-block;width:100%;padding:12px;border-radius:999px;background:linear-gradient(90deg,var(--primary),#60a5fa);color:#fff;border:none;font-weight:700;cursor:pointer}
        .btn-muted{background:#f1f5f9;color:#0f172a;border:none;padding:10px;border-radius:8px;cursor:pointer}
        .small-note{font-size:0.9rem;color:var(--muted);margin-top:8px}
        #debug-response{margin-top:12px;font-size:0.9rem;color:#0f172a;background:#fff5f5;padding:8px;border-radius:8px;border:1px solid #ffd7d7;display:none}
        .dev-marker{font-size:12px;color:#555;margin-bottom:8px}
        @media (max-width:900px){.wrap{flex-direction:column}}
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <h1>Solicitar Servicio</h1>
            <p class="lead">Rellena tus datos y te mostraremos el resumen con el botón de pago.</p>
            <!-- DEV MARKER: UI actualizado - v2 -->
            <div class="dev-marker">DEV MARKER: UI actualizado - v2</div>

            <form id="ordenForm" action="{{ url('/procesar-orden') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col">
                        <label for="nombre">Nombre</label>
                        <input id="nombre" name="nombre" type="text" placeholder="Tu nombre completo" required>
                    </div>
                    <div class="col">
                        <label for="telefono">Teléfono</label>
                        <input id="telefono" name="telefono" type="text" placeholder="555-123-456" required>
                    </div>
                </div>

                <label for="direccion">Dirección</label>
                <input id="direccion" name="direccion" type="text" placeholder="Calle 123, Ciudad" required>

                <div class="row">
                    <div class="col">
                        <label for="servicio">Servicio</label>
                        <select id="servicio" name="servicio" required>
                            <option value="lavado">Lavado — Q25</option>
                            <option value="secado">Secado — Q15</option>
                            <option value="aspirado">Aspirado — Q20</option>
                            <option value="lavado_secado">Lavado + Secado — Q35</option>
                        </select>
                    </div>
                    <div class="col">
                        <label for="entrega">Entrega</label>
                        <select id="entrega" name="entrega" required>
                            <option value="recoger_empresa">Recoger en empresa</option>
                            <option value="entrega_domicilio">Entrega a domicilio (+Q10)</option>
                        </select>
                    </div>
                </div>

                <label for="pago">Método de pago</label>
                <select id="pago" name="pago" required>
                    <option value="paypal">Tarjeta / PayPal</option>
                </select>

                <div style="margin-top:18px">
                    <button id="submitBtn" class="btn-primary" type="submit">Solicitar servicio — Continuar</button>
                </div>
                <div id="debug-response"></div>
            </form>
            <p class="small-note">Si JavaScript está deshabilitado, el formulario se enviará normalmente y verás la página de confirmación.</p>
        </div>

        <aside class="card small" id="summaryCard">
            <div style="display:flex;justify-content:space-between;align-items:center">
                <div>
                    <div class="badge">Resumen</div>
                    <div class="muted" style="margin-top:8px">Tu orden se mostrará aquí antes de pagar.</div>
                </div>
                <div class="total" id="calculatedTotal">Q25</div>
            </div>

            <div id="payButtonContainer" style="margin-top:18px"></div>
            <div style="margin-top:12px">
                <a href="{{ url('/') }}" class="btn-muted">Volver al inicio</a>
            </div>
        </aside>
    </div>

    <script>
        (function(){
            const form = document.getElementById('ordenForm');
            const submitBtn = document.getElementById('submitBtn');
            const payContainer = document.getElementById('payButtonContainer');
            const debug = document.getElementById('debug-response');
            const totalEl = document.getElementById('calculatedTotal');

            // Función de ayuda para obtener el token CSRF de manera segura
            function getCsrfToken() {
                const token = document.querySelector('meta[name="csrf-token"]');
                if (!token || !token.content) {
                    debug.style.display = 'block';
                    debug.innerText = 'Error: Token CSRF no encontrado. Por favor, recarga la página.';
                    throw new Error('Token CSRF no encontrado');
                }
                return token.content;
            }

            // Manejador global de errores
            window.addEventListener('error', function(event) {
                console.error('Error JS:', event.error);
                debug.style.display = 'block';
                debug.innerText = 'Error: ' + (event.error?.message || 'Error desconocido');
            });

            function calcTotal(){
                const servicio = document.getElementById('servicio').value;
                const entrega = document.getElementById('entrega').value;
                let total = 0;
                if (servicio === 'lavado') total = 25;
                if (servicio === 'secado') total = 15;
                if (servicio === 'aspirado') total = 20;
                if (servicio === 'lavado_secado') total = 35;
                if (entrega === 'entrega_domicilio') total += 10;
                totalEl.innerText = 'Q' + total.toFixed(2);
                return total;
            }

            // live calc
            document.getElementById('servicio').addEventListener('change', calcTotal);
            document.getElementById('entrega').addEventListener('change', calcTotal);
            calcTotal();

            form.addEventListener('submit', function(e){
                e.preventDefault();

                // disable button
                submitBtn.disabled = true;
                const originalText = submitBtn.innerText;
                submitBtn.innerText = 'Preparando pago...';

                const formData = new FormData(form);

                // Mostrar mensaje de procesamiento
                debug.style.display = 'block';
                debug.innerText = 'Procesando solicitud...';

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: formData,
                    credentials: 'same-origin'
                }).then(async res => {
                    // If server redirected to a normal page it won't be JSON — handle gracefully
                    const contentType = res.headers.get('content-type') || '';
                    if (!res.ok) {
                        const text = await res.text();
                        throw new Error('Status ' + res.status + ' — ' + text.substring(0,250));
                    }
                    if (contentType.indexOf('application/json') === -1) {
                        // server returned HTML (maybe non-AJAX fallback) — replace body
                        const html = await res.text();
                        debug.style.display = 'block';
                        debug.innerText = 'Respuesta inesperada (HTML).';
                        // As fallback, navigate to response so user sees server page
                        document.open(); document.write(html); document.close();
                        return;
                    }
                    return res.json();
                }).then(data => {
                    if (!data) return;
                    console.log('Respuesta /procesar-orden:', data);
                    // show raw response in debug area when helpful
                    debug.style.display = 'block';
                    debug.innerText = JSON.stringify(data, null, 2).substring(0,800);

                    // Restaurar el botón a su estado original
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalText;

                    if (data.approve_url) {
                        // PayPal payment flow
                        console.log('Iniciando flujo de PayPal');
                        payContainer.innerHTML = `
                            <div class="paypal-inline-form">
                                <div id="paypal-button-container"></div>
                                <div class="small-note" style="margin-top:12px">
                                    Al hacer clic en PayPal, serás redirigido a su sitio seguro para completar el pago.
                                </div>
                            </div>
                        `;
                        payContainer.scrollIntoView({behavior:'smooth'});

                        // Render the PayPal button
                        paypal.Buttons({
                            createOrder: function() {
                                return fetch('/process-paypal-payment', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: JSON.stringify({
                                        orden_id: data.orden_id
                                    })
                                }).then(function(res) {
                                    return res.json();
                                }).then(function(orderData) {
                                    return orderData.id;
                                });
                            },
                            onApprove: function(data) {
                                // Show a success message
                                payContainer.innerHTML = `
                                    <div style="text-align:center;padding:20px 0">
                                        <div style="color:#059669;font-weight:bold;margin-bottom:8px">
                                            ¡Pago completado con éxito!
                                        </div>
                                        <div class="small-note">
                                            Redirigiendo a tu orden...
                                        </div>
                                    </div>
                                `;
                                
                                // Redirect to the order page after 2 seconds
                                setTimeout(function() {
                                    window.location.href = '/orden/' + data.orden_id;
                                }, 2000);
                            },
                            onError: function(err) {
                                console.error('Error en PayPal:', err);
                                debug.style.display = 'block';
                                debug.innerText = 'Error al procesar el pago: ' + err.message;
                                // Re-enable form
                                submitBtn.disabled = false;
                                submitBtn.innerText = originalText;
                            }
                        }).render('#paypal-button-container');

                    } else if (data.html && data.orden) {
                        debug.style.display = 'block';
                        debug.innerText = 'Orden creada correctamente. Iniciando PayPal...';
                        
                        try {
                            // Mostrar el resumen de la orden y preparar el contenedor de PayPal
                            payContainer.innerHTML = `
                                <div class="order-summary">
                                    <div style="margin-bottom:16px">
                                        <strong>Orden #${data.orden.id}</strong><br>
                                        Total a pagar: Q${data.orden.total.toFixed(2)}
                                    </div>
                                    <div id="paypal-button-container"></div>
                                    <div class="small-note" style="margin-top:12px">
                                        Da clic en PayPal para realizar el pago seguro.
                                    </div>
                                </div>
                            `;
                            payContainer.scrollIntoView({behavior:'smooth'});

                            // Inicializar el botón de PayPal
                            paypal.Buttons({
                                style: {
                                    layout: 'vertical',
                                    color: 'gold',
                                    shape: 'rect',
                                    label: 'pay'
                                },
                                fundingSource: paypal.FUNDING.PAYPAL,
                                createOrder: function() {
                                    debug.innerText = 'Iniciando proceso de pago...';
                                    
                                    return fetch('/paypal/payment', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': getCsrfToken(),
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            orden_id: data.orden.id
                                        })
                                    })
                                    .then(response => {
                                        if (!response.ok) {
                                            return response.text().then(text => {
                                                throw new Error('Error en la respuesta del servidor: ' + text);
                                            });
                                        }
                                        return response.json();
                                    })
                                    .then(orderData => {
                                        if (!orderData.paypal_order_id) {
                                            throw new Error('No se recibió ID de orden de PayPal');
                                        }
                                        debug.innerText = 'Orden de PayPal creada: ' + orderData.paypal_order_id;
                                        return orderData.paypal_order_id;
                                    });
                                },
                                onApprove: function(paypalData, actions) {
                                    debug.innerText = 'Confirmando pago...';
                                    return actions.order.capture().then(function() {
                                        debug.innerText = '¡Pago completado! Redirigiendo...';
                                        
                                        // Obtener el token CSRF de manera segura
                                        const csrfToken = document.querySelector('meta[name="csrf-token"]');
                                        if (!csrfToken) {
                                            debug.innerText = 'Error: Token CSRF no encontrado';
                                            throw new Error('Token CSRF no encontrado');
                                        }
                                        
                                        // Notificar al servidor que el pago fue exitoso
                                        return fetch('/confirm-payment', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': csrfToken.content
                                            },
                                            body: JSON.stringify({
                                                orden_id: data.orden.id,
                                                paypal_order_id: paypalData.orderID
                                            })
                                        }).then(function() {
                                            setTimeout(function() {
                                                window.location.href = '/orden/' + data.orden.id;
                                            }, 2000);
                                        });
                                    });
                                },
                                onError: function(err) {
                                    console.error('Error en PayPal:', err);
                                    debug.innerText = 'Error al procesar el pago: ' + err.message;
                                }
                            }).render('#paypal-button-container');

                        } catch (ex) {
                            console.error('Error al mostrar el resumen:', ex);
                            if (data.orden && data.orden.id) {
                                window.location.href = '/orden/' + data.orden.id;
                            }
                        }
                    } else {
                        // No hay datos útiles en la respuesta
                        debug.style.display = 'block';
                        debug.innerText = 'Error: No se pudo procesar la respuesta del servidor';
                    }
                }).catch(err => {
                    console.error('Error al crear orden:', err);
                    debug.style.display = 'block';
                    debug.innerText = 'Error: ' + (err.message || err);
                    // re-enable button and fallback to normal submit
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Solicitar servicio — Reintentar';
                });
            });

        })();
    </script>
</body>
</html>