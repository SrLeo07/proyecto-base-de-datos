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
        :root{--primary:#1976d2;--accent:#43a047;--muted:#6b7280;--card:#ffffff}
        *{box-sizing:border-box}
        body{font-family:'Segoe UI', Arial, sans-serif;background:linear-gradient(120deg, #b2ebf2 0%, #e1bee7 100%);margin:0;padding:28px;min-height:100vh}
        .bubbles {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            z-index: 0;
            pointer-events: none;
        }
        .wrap{position:relative;z-index:1;max-width:920px;margin:20px auto;display:flex;gap:24px;align-items:flex-start}
        .card{background:rgba(255,255,255,0.97);border-radius:24px;padding:28px;box-shadow:0 8px 32px 0 rgba(31,38,135,0.18);flex:1}
        .card.small{max-width:380px}
        h1{margin:0 0 8px;color:var(--primary);font-size:1.6rem;font-weight:700}
        p.lead{margin:0 0 18px;color:var(--accent);font-size:1.1rem}
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
        @media (max-width:900px){.wrap{flex-direction:column}}
    </style>
</head>
<body>
    <div class="bubbles">
        <svg width="100%" height="100%">
            <circle cx="10%" cy="20%" r="32" fill="#b2ebf2" opacity="0.3"/>
            <circle cx="80%" cy="10%" r="18" fill="#81d4fa" opacity="0.25"/>
            <circle cx="60%" cy="80%" r="24" fill="#e1bee7" opacity="0.22"/>
            <circle cx="30%" cy="60%" r="14" fill="#b3e5fc" opacity="0.18"/>
            <circle cx="90%" cy="60%" r="22" fill="#ce93d8" opacity="0.18"/>
        </svg>
    </div>
    <div class="wrap">
        <div class="card">
            <h1>Solicitar Servicio</h1>
            <p class="lead">Rellena tus datos y te mostraremos el resumen con el botón de pago.</p>

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

                <div id="direccion-container" style="display: none;">
                    <label for="direccion">Dirección de entrega</label>
                    <input id="direccion" name="direccion" type="text" placeholder="Calle 123, Ciudad">
                </div>

                <div class="row">
                    <div class="col">
                        <label for="servicio">Servicio</label>
                        <select id="servicio" name="servicio" required>
                            <option value="lavado">Lavado — $3.25</option>
                            <option value="secado">Secado — $2.00</option>
                            <option value="aspirado">Aspirado — $2.50</option>
                            <option value="lavado_secado">Lavado + Secado — $4.50</option>
                        </select>
                    </div>
                    <div class="col">
                        <label for="entrega">Entrega</label>
                        <select id="entrega" name="entrega" required>
                            <option value="recoger_empresa">Recoger en empresa</option>
                            <option value="entrega_domicilio">Entrega a domicilio (+$1.25)</option>
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
            </form>
        </div>

        <aside class="card small" id="summaryCard">
            <div style="display:flex;justify-content:space-between;align-items:center">
                <div>
                    <div class="badge">Resumen</div>
                    <div class="muted" style="margin-top:8px">Tu orden se mostrará aquí antes de pagar.</div>
                </div>
                <div class="total" id="calculatedTotal">$3.25</div>
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
                if (servicio === 'lavado') total = 3.25;
                if (servicio === 'secado') total = 2.00;
                if (servicio === 'aspirado') total = 2.50;
                if (servicio === 'lavado_secado') total = 4.50;
                if (entrega === 'entrega_domicilio') total += 1.25;
                totalEl.innerText = '$' + total.toFixed(2);
                return total;
            }

            // live calc
            const entregaSelect = document.getElementById('entrega');
            const direccionContainer = document.getElementById('direccion-container');
            const direccionInput = document.getElementById('direccion');

            function toggleDireccion() {
                const isDelivery = entregaSelect.value === 'entrega_domicilio';
                direccionContainer.style.display = isDelivery ? 'block' : 'none';
                direccionInput.required = isDelivery;
                if (!isDelivery) {
                    direccionInput.value = '';
                }
            }

            document.getElementById('servicio').addEventListener('change', calcTotal);
            entregaSelect.addEventListener('change', function() {
                calcTotal();
                toggleDireccion();
            });
            
            // Inicializar estados
            calcTotal();
            toggleDireccion();

            form.addEventListener('submit', function(e){
                e.preventDefault();

                // disable button
                submitBtn.disabled = true;
                const originalText = submitBtn.innerText;
                submitBtn.innerText = 'Preparando pago...';

                const formData = new FormData(form);

                // Validar el formulario antes de enviar
                if (entregaSelect.value === 'entrega_domicilio' && !direccionInput.value.trim()) {
                    alert('Por favor ingresa la dirección de entrega');
                    direccionInput.focus();
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalText;
                    return;
                }

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
                    const contentType = res.headers.get('content-type') || '';
                    
                    if (!res.ok) {
                        if (contentType.includes('application/json')) {
                            return res.json().then(err => {
                                throw new Error(err.message || 'Error al procesar la orden');
                            });
                        }
                        const text = await res.text();
                        throw new Error('Error al procesar la orden');
                    }

                    if (!contentType.includes('application/json')) {
                        const html = await res.text();
                        document.open(); 
                        document.write(html); 
                        document.close();
                        return;
                    }

                    return res.json();
                }).then(data => {
                    if (!data) return;
                    console.log('Respuesta /procesar-orden:', data);
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
                                return fetch('/paypal/payment', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': getCsrfToken(),
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        orden_id: data.orden_id
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
                            onApprove: function(data, actions) {
                                debug.innerText = 'Confirmando pago...';
                                return actions.order.capture().then(function() {
                                    debug.innerText = '¡Pago completado! Redirigiendo...';
                                    window.location.href = '/paypal/success?token=' + data.orderID;
                                });
                            },
                            onError: function(err) {
                                console.error('Error en PayPal:', err);
                                debug.style.display = 'block';
                                debug.innerText = 'Error al procesar el pago: ' + err.message;
                                submitBtn.disabled = false;
                                submitBtn.innerText = originalText;
                            },
                            onCancel: function() {
                                window.location.href = '/paypal/cancel';
                            }
                        }).render('#paypal-button-container');

                    } else if (data.html && data.orden) {
                        try {
                            // Mostrar el resumen de la orden y preparar el contenedor de PayPal
                            payContainer.innerHTML = `
                                <div class="order-summary" data-orden-id="${data.orden.id}">
                                    <div style="margin-bottom:16px">
                                        <strong>Orden #${data.orden.id}</strong><br>
                                        Total a pagar: $${parseFloat(data.orden.total).toFixed(2)}
                                    </div>
                                    <div id="paypal-button-container"></div>
                                    <div class="small-note" style="margin-top:12px">
                                        Da clic en PayPal para realizar el pago seguro.
                                    </div>
                                </div>
                            `;
                            payContainer.scrollIntoView({behavior:'smooth'});

                            // Guardar el ID de la orden en una variable accesible
                            const ordenId = data.orden.id;
                            
                            // Inicializar el botón de PayPal con la misma configuración que arriba
                            paypal.Buttons({
                                style: {
                                    layout: 'vertical',
                                    color: 'gold',
                                    shape: 'rect',
                                    label: 'pay'
                                },
                                createOrder: function() {
                                                    return fetch('/paypal/payment', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': getCsrfToken(),
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            orden_id: ordenId
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
                                        return orderData.paypal_order_id;
                                    });
                                },
                                onApprove: function(data, actions) {
                                    return actions.order.capture()
                                        .then(function(details) {
                                            console.log('PayPal capture details:', details);
                                            // Usar el ordenId del closure
                                            if (!ordenId) {
                                                throw new Error('No se encontró el ID de la orden');
                                            }
                                            
                                            // Enviar los detalles del pago al servidor
                                            return fetch('/paypal/success', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': getCsrfToken(),
                                                'Accept': 'application/json'
                                            },
                                            body: JSON.stringify({
                                                orderID: data.orderID,
                                                payerID: data.payerID,
                                                token: data.orderID,
                                                orden_id: ordenId,
                                                details: details
                                            })
                                        })
                                        .then(response => {
                                            if (!response.ok) {
                                                return response.json().then(err => {
                                                    throw new Error(err.message || 'Error al procesar el pago');
                                                });
                                            }
                                            return response.json();
                                        })
                                        .then(result => {
                                            if (!result.success) {
                                                throw new Error(result.message || 'Error al actualizar el estado de la orden');
                                            }
                                            window.location.href = '/orden/' + ordenId;
                                        })
                                        .catch(error => {
                                            debug.style.color = 'red';
                                            debug.innerText = 'Error: ' + error.message;
                                            console.error('Error:', error);
                                            
                                            // Mostrar un botón para reintentar
                                            const retryButton = document.createElement('button');
                                            retryButton.innerText = 'Reintentar';
                                            retryButton.className = 'btn btn-primary mt-3';
                                            retryButton.onclick = () => window.location.reload();
                                            debug.parentNode.insertBefore(retryButton, debug.nextSibling);
                                        });
                                    });
                                },
                                onError: function(err) {
                                    console.error('Error en PayPal:', err);
                                    debug.innerText = 'Error al procesar el pago: ' + err.message;
                                },
                                onCancel: function() {
                                    window.location.href = '/paypal/cancel';
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
                    alert(err.message || 'Ocurrió un error al procesar la orden. Por favor intenta nuevamente.');
                    // re-enable button
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Solicitar servicio — Reintentar';
                });
            });

        })();
    </script>
</body>
</html>