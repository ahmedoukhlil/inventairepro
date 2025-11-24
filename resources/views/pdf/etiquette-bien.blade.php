<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            width: 70mm;
            height: 37mm;
            margin: 0;
            padding: 2mm;
            border: 2px solid #000;
            box-sizing: border-box;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1mm;
        }
        .content-table td {
            vertical-align: top;
            padding: 0;
        }
        .qr-container {
            width: 25mm;
            height: 25mm;
            border: 1px solid #ddd;
            padding: 0.5mm;
            background: #fff;
            text-align: center;
        }
        .qr-container img {
            width: 24mm;
            height: 24mm;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }
        .info-section {
            padding-left: 2mm;
            width: auto;
        }
        .code-inventaire {
            font-size: 12pt;
            font-weight: bold;
            text-align: left;
            color: #000;
            letter-spacing: 0.5pt;
            line-height: 1.2;
            margin-bottom: 1mm;
            word-break: break-all;
        }
        .designation {
            font-size: 8pt;
            text-align: left;
            margin-bottom: 1mm;
            line-height: 1.2;
            max-height: 10mm;
            overflow: hidden;
            font-weight: 500;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 6pt;
            margin-bottom: 0.3mm;
            line-height: 1.1;
        }
        .info-label {
            color: #666;
            font-weight: normal;
            margin-right: 2mm;
        }
        .info-value {
            color: #000;
            font-weight: 500;
            text-align: right;
            flex: 1;
        }
        .footer {
            border-top: 1px solid #ddd;
            padding-top: 0.5mm;
            margin-top: 1mm;
            font-size: 6pt;
            color: #666;
            text-align: center;
            width: 100%;
        }
    </style>
</head>
<body>
    <table class="content-table">
        <tr>
            <td style="width: 25mm;">
                <div class="qr-container">
                    @if($bien->qr_code_path && Storage::disk('public')->exists($bien->qr_code_path))
                        @if(str_ends_with($bien->qr_code_path, '.svg'))
                            @php
                                $svgPath = storage_path('app/public/' . $bien->qr_code_path);
                                $svgContent = file_get_contents($svgPath);
                                // Nettoyer et ajuster le SVG pour DomPDF
                                $svgContent = preg_replace('/width="[^"]*"/', '', $svgContent);
                                $svgContent = preg_replace('/height="[^"]*"/', '', $svgContent);
                                // Convertir en base64 pour une meilleure compatibilité avec DomPDF
                                $base64Svg = base64_encode($svgContent);
                            @endphp
                            <img src="data:image/svg+xml;base64,{{ $base64Svg }}" alt="QR Code" style="width: 24mm; height: 24mm; object-fit: contain; display: block;">
                        @else
                            <img src="{{ public_path('storage/' . $bien->qr_code_path) }}" alt="QR Code" style="width: 24mm; height: 24mm; object-fit: contain;">
                        @endif
                    @else
                        <div style="width: 100%; height: 100%; background: #f0f0f0; padding: 5mm 0; text-align: center;">
                            <span style="color: #999; font-size: 6pt;">QR Code<br>non disponible</span>
                        </div>
                    @endif
                </div>
            </td>
            <td>
                <div class="info-section">
                    <div class="code-inventaire">{{ $bien->code_inventaire }}</div>
                    <div class="designation">{{ Str::limit($bien->designation, 50) }}</div>
                    
                    <div>
                        @if($bien->localisation)
                            <div class="info-row">
                                <span class="info-label">Localisation:</span>
                                <span class="info-value">{{ $bien->localisation->code }}</span>
                            </div>
                        @endif
                        
                        @if($bien->nature)
                            <div class="info-row">
                                <span class="info-label">Nature:</span>
                                <span class="info-value">{{ $bien->nature }}</span>
                            </div>
                        @endif
                        
                        @if($bien->date_acquisition)
                            <div class="info-row">
                                <span class="info-label">Date:</span>
                                <span class="info-value">{{ $bien->date_acquisition->format('d/m/Y') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </td>
        </tr>
    </table>
    
    <div class="footer">
        {{ config('app.name', 'Inventaire Pro') }} - {{ now()->format('Y') }}
    </div>
</body>
</html>

