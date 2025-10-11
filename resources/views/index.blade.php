<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lavandería Express - Inicio</title>
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(120deg, #b2ebf2 0%, #e1bee7 100%);
            overflow-x: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .bubbles {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            z-index: 0;
            pointer-events: none;
        }
        .container {
            position: relative;
            z-index: 1;
            background: rgba(255,255,255,0.97);
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.18);
            border-radius: 24px;
            padding: 48px 36px 36px 36px;
            max-width: 600px;
            width: 100%;
            margin: 0;
        }
        .header-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-bottom: 18px;
        }
        .header-logo svg {
            width: 54px;
            height: 54px;
        }
        h1 {
            color: #1976d2;
            font-size: 2.7rem;
            margin: 0;
            letter-spacing: 2px;
            font-weight: 700;
        }
        .subtitle {
            text-align: center;
            color: #43a047;
            font-size: 1.2rem;
            margin-bottom: 30px;
            font-weight: 500;
        }
        .services-list {
            display: flex;
            gap: 18px;
            justify-content: center;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }
        .service-card {
            background: #e3f2fd;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(33,150,243,0.10);
            padding: 22px 18px 14px 18px;
            min-width: 120px;
            text-align: center;
            transition: transform 0.18s, box-shadow 0.18s;
        }
        .service-card:hover {
            transform: translateY(-6px) scale(1.04);
            box-shadow: 0 6px 18px rgba(33,150,243,0.18);
        }
        .service-card svg {
            width: 38px;
            height: 38px;
            margin-bottom: 8px;
        }
        .service-title {
            color: #1976d2;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .service-price {
            color: #43a047;
            font-size: 1rem;
            font-weight: 500;
        }
        h2 {
            color: #7b1fa2;
            margin-top: 32px;
            margin-bottom: 10px;
            font-size: 1.3rem;
        }
        ul {
            padding-left: 20px;
            margin-bottom: 20px;
            color: #333;
            font-size: 1.08rem;
        }
        table {
            background: #f3e5f5;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 24px;
            width: 100%;
            box-shadow: 0 1px 6px rgba(123,31,162,0.07);
        }
        th, td {
            padding: 10px 18px;
            text-align: left;
        }
        th {
            background: #7b1fa2;
            color: #fff;
        }
        td {
            color: #333;
        }
        .button, a.button {
            display: inline-block;
            background: linear-gradient(90deg, #1976d2 60%, #43a047 100%);
            color: #fff;
            padding: 16px 38px;
            border-radius: 30px;
            font-size: 1.25rem;
            font-weight: bold;
            text-decoration: none;
            text-align: center;
            margin: 0 auto;
            box-shadow: 0 4px 12px rgba(33,150,243,0.13);
            transition: background 0.2s, transform 0.2s;
            border: none;
            cursor: pointer;
            margin-top: 18px;
        }
        .button:hover, a.button:hover {
            background: linear-gradient(90deg, #43a047 60%, #1976d2 100%);
            transform: translateY(-2px) scale(1.04);
        }
        @media (max-width: 700px) {
            .container {
                padding: 18px 4px;
            }
            .services-list {
                flex-direction: column;
                gap: 12px;
            }
        }
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
    <div class="container">
        <div class="header-logo">
            <svg fill="none" viewBox="0 0 64 64"><circle cx="32" cy="32" r="32" fill="#b3e5fc"/><ellipse cx="32" cy="44" rx="22" ry="12" fill="#81d4fa"/><ellipse cx="32" cy="44" rx="16" ry="7" fill="#4fc3f7"/><circle cx="32" cy="28" r="12" fill="#fff"/><ellipse cx="32" cy="28" rx="8" ry="5" fill="#b3e5fc"/></svg>
            <h1>Lavandería Express</h1>
        </div>
        <div class="subtitle">
            ¡Ropa limpia, fresca y lista para ti!
        </div>
        <div class="services-list">
            <div class="service-card">
                <svg fill="none" viewBox="0 0 48 48"><circle cx="24" cy="24" r="20" fill="#b3e5fc"/><ellipse cx="24" cy="32" rx="14" ry="8" fill="#81d4fa"/><ellipse cx="24" cy="32" rx="10" ry="5" fill="#4fc3f7"/></svg>
                <div class="service-title">Lavado</div>
                <div class="service-price">Q25</div>
            </div>
            <div class="service-card">
                <svg fill="none" viewBox="0 0 48 48"><circle cx="24" cy="24" r="20" fill="#ffe082"/><path d="M24 14v20M14 24h20" stroke="#ffb300" stroke-width="3" stroke-linecap="round"/></svg>
                <div class="service-title">Secado</div>
                <div class="service-price">Q15</div>
            </div>
            <div class="service-card">
                <svg fill="none" viewBox="0 0 48 48"><rect x="10" y="20" width="28" height="14" rx="7" fill="#b2dfdb"/><rect x="16" y="26" width="16" height="6" rx="3" fill="#26a69a"/></svg>
                <div class="service-title">Aspirado</div>
                <div class="service-price">Q20</div>
            </div>
            <div class="service-card">
                <svg fill="none" viewBox="0 0 48 48"><ellipse cx="24" cy="24" rx="20" ry="12" fill="#b2ebf2"/><ellipse cx="24" cy="24" rx="14" ry="7" fill="#81d4fa"/><ellipse cx="24" cy="24" rx="10" ry="4" fill="#4fc3f7"/></svg>
                <div class="service-title">Lavado + Secado</div>
                <div class="service-price">Q35</div>
            </div>
        </div>
        <h2>¿Cómo funciona?</h2>
        <ul>
            <li>Solicita tu servicio en línea.</li>
            <li>Nuestros motoristas recogen tu ropa en tu domicilio o puedes traerla a la empresa.</li>
            <li>Elige si deseas recoger tu ropa o recibirla en tu domicilio.</li>
            <li>Paga de forma segura con tarjeta de crédito vía Paypal.</li>
        </ul>
        <h2>Tarifas adicionales</h2>
        <table>
            <tr>
                <th>Servicio</th>
                <th>Precio</th>
            </tr>
            <tr>
                <td>Recogida o entrega a domicilio</td>
                <td>Q10 extra</td>
            </tr>
        </table>
        <div style="text-align:center;">
            <a href="{{ url('/orden') }}" class="button">Solicitar Servicio</a>
        </div>
    </div>
</body>
</html>