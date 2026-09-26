<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ficha del cliente {{ $cliente->codigo }}</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: #f2f5f4;
            color: #1d2926;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            line-height: 1.45;
        }

        .acciones {
            display: flex;
            justify-content: flex-end;
            max-width: 900px;
            margin: 24px auto 12px;
        }

        .boton {
            border: 0;
            border-radius: 4px;
            padding: 10px 16px;
            background: #176b58;
            color: #fff;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }

        .ficha {
            max-width: 900px;
            margin: 0 auto 32px;
            padding: 36px 42px;
            background: #fff;
            border: 1px solid #d6dfdc;
        }

        .encabezado {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 24px;
            padding-bottom: 20px;
            border-bottom: 2px solid #176b58;
        }

        .entidad {
            margin: 0 0 5px;
            color: #176b58;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        h1 { margin: 0; font-size: 25px; }

        .fecha {
            margin: 5px 0 0;
            color: #5d6b67;
            text-align: right;
        }

        section { margin-top: 24px; }

        h2 {
            margin: 0 0 12px;
            font-size: 16px;
        }

        .datos {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px 28px;
        }

        .dato { min-width: 0; }
        .etiqueta { display: block; color: #5d6b67; font-size: 12px; }
        .valor { display: block; overflow-wrap: anywhere; font-weight: 600; }

        .resumen {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 16px 18px;
            border: 1px solid #d6dfdc;
            border-left: 4px solid #176b58;
        }

        .estado { margin: 4px 0 0; font-weight: 700; }
        .estado--vencido { color: #a52e27; }
        .estado--pendiente { color: #8a5b06; }
        .estado--al_dia { color: #176b58; }
        .saldo { font-size: 20px; font-weight: 700; white-space: nowrap; }
        .vencimiento { margin: 8px 0 0; color: #5d6b67; }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th, td { padding: 10px 8px; border-bottom: 1px solid #d6dfdc; vertical-align: top; }
        th { color: #5d6b67; font-size: 12px; font-weight: 700; }
        td { overflow-wrap: anywhere; }
        .vacio { padding: 16px 8px; color: #5d6b67; }

        @media (max-width: 620px) {
            .acciones { margin: 12px; }
            .ficha { margin: 0 12px 16px; padding: 24px 18px; }
            .encabezado { flex-direction: column; }
            .fecha { text-align: left; }
            .datos { grid-template-columns: 1fr; }
            .tabla-contadores { overflow-x: auto; }
            table { min-width: 560px; }
        }

        @page { size: A4 portrait; margin: 14mm; }

        @media print {
            body { background: #fff; color: #000; font-size: 11pt; }
            .no-imprimir { display: none !important; }
            .ficha { max-width: none; margin: 0; padding: 0; border: 0; }
            .encabezado { border-color: #000; }
            .entidad, .etiqueta, .fecha, .vencimiento, th { color: #333; }
            .resumen { border-color: #777; border-left-color: #000; }
            .estado--vencido, .estado--pendiente, .estado--al_dia { color: #000; }
            th, td { border-color: #999; }
            tr, .resumen, .dato { break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="acciones no-imprimir">
        <button class="boton" type="button" onclick="window.print()">Imprimir ficha</button>
    </div>

    <main class="ficha">
        <header class="encabezado">
            <div>
                <p class="entidad">Gestión de agua potable</p>
                <h1>Ficha del cliente</h1>
            </div>
            <p class="fecha">Generada el {{ now()->format('d/m/Y H:i') }}</p>
        </header>

        <section aria-labelledby="datos-cliente">
            <h2 id="datos-cliente">Datos del cliente</h2>
            <div class="datos">
                <div class="dato">
                    <span class="etiqueta">Código</span>
                    <span class="valor">{{ $cliente->codigo }}</span>
                </div>
                <div class="dato">
                    <span class="etiqueta">Nombre</span>
                    <span class="valor">{{ $cliente->nombre }}</span>
                </div>
                <div class="dato">
                    <span class="etiqueta">Teléfono</span>
                    <span class="valor">{{ $cliente->telefono ?: 'Sin teléfono registrado' }}</span>
                </div>
                <div class="dato">
                    <span class="etiqueta">Correo electrónico</span>
                    <span class="valor">{{ $cliente->email ?: 'Sin correo registrado' }}</span>
                </div>
                <div class="dato">
                    <span class="etiqueta">Dirección de notificación</span>
                    <span class="valor">{{ $cliente->direccion_notificacion ?: 'Sin dirección registrada' }}</span>
                </div>
                <div class="dato">
                    <span class="etiqueta">Estado del cliente</span>
                    <span class="valor">{{ $cliente->estado === 'activo' ? 'Activo' : 'Inactivo' }}</span>
                </div>
            </div>
        </section>

        <section aria-labelledby="estado-cuenta">
            <h2 id="estado-cuenta">Estado de cuenta</h2>
            <div class="resumen">
                <div>
                    <span class="etiqueta">Situación de pagos</span>
                    <p class="estado estado--{{ $cliente->estado_de_cuenta }}">
                        {{ match ($cliente->estado_de_cuenta) {
                            'al_dia' => 'Al día',
                            'vencido' => 'Con pagos vencidos',
                            default => 'Con saldo pendiente',
                        } }}
                    </p>
                    @if ($venceMasAntigua)
                        <p class="vencimiento">Vencimiento pendiente más antiguo: {{ $venceMasAntigua->format('d/m/Y') }}</p>
                    @endif
                </div>
                <div class="saldo">Q {{ number_format($cliente->deuda_total, 2) }}</div>
            </div>
        </section>

        <section aria-labelledby="contadores-cliente">
            <h2 id="contadores-cliente">Contadores y servicios</h2>
            @if ($cliente->contadores->isNotEmpty())
                <div class="tabla-contadores">
                    <table>
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Dirección del servicio</th>
                                <th>Paja</th>
                                <th>Estado</th>
                                <th>Instalación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cliente->contadores as $contador)
                                <tr>
                                    <td>{{ $contador->codigo }}</td>
                                    <td>
                                        {{ $contador->predio?->direccion_completa ?: 'Sin dirección registrada' }}
                                        @if ($contador->predio?->sector)
                                            <br><span class="etiqueta">Sector: {{ $contador->predio->sector->nombre }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $contador->paja?->nombre ?: '—' }}</td>
                                    <td>{{ ucfirst($contador->estado) }}</td>
                                    <td>{{ $contador->fecha_instalacion?->format('d/m/Y') ?: '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="vacio">Este cliente no tiene contadores registrados.</p>
            @endif
        </section>
    </main>
</body>
</html>