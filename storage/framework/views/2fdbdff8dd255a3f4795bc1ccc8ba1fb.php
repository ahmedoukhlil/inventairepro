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
        @page {
            margin: 5mm;
            size: A4 portrait;
        }
        body {
            font-family: Arial, sans-serif;
        }
        .page {
            width: 100%;
            display: grid;
            /* 3 colonnes : (210mm - 10mm marges) / 3 = ~66.67mm par colonne */
            /* Avec espacement de 2mm entre colonnes : (200mm - 4mm) / 3 = ~65.33mm */
            grid-template-columns: repeat(3, 1fr);
            /* 7 lignes : (297mm - 10mm marges) / 7 = ~41mm par ligne */
            /* Avec espacement de 2mm entre lignes : (287mm - 12mm) / 7 = ~39.29mm */
            grid-template-rows: repeat(7, 1fr);
            gap: 2mm;
            page-break-after: always;
            height: 287mm; /* 297mm - 10mm marges */
        }
        .etiquette {
            width: 100%;
            height: 100%;
            padding: 1mm;
            border: 0.5px solid #ddd;
            page-break-inside: avoid;
            box-sizing: border-box;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: white;
        }
        .barcode-wrapper {
            width: 100%;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.5mm;
            overflow: hidden;
            min-height: 20mm;
        }
        .barcode-container {
            width: 100%;
            min-width: 37.3mm; /* Largeur minimale Code 128 */
            max-width: 100%;
            text-align: center;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .code-text {
            width: 100%;
            text-align: center;
            font-size: 5pt;
            font-weight: normal;
            font-family: 'Courier New', monospace;
            color: #000;
            padding: 0.5mm 0;
            letter-spacing: 0.1pt;
            line-height: 1.1;
            word-break: break-all;
        }
        svg {
            width: 100% !important;
            min-width: 37.3mm !important;
            height: auto !important;
            min-height: 12.7mm !important;
            max-width: 100% !important;
            max-height: 100% !important;
            display: block;
        }
        table {
            margin: 0 auto !important;
            width: auto !important;
            max-width: 100% !important;
            border-collapse: collapse !important;
        }
        table td {
            padding: 0 !important;
            border: 0 !important;
            margin: 0 !important;
        }
        div[style*="display: inline-block"] {
            display: inline-block !important;
        }
        .header-info {
            text-align: center;
            font-size: 8pt;
            font-weight: bold;
            margin-bottom: 1mm;
            color: #333;
        }
    </style>
</head>
<body>
    <?php
        // Grouper les biens par page (21 étiquettes par page)
        $biensChunked = $biens->chunk(21);
    ?>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $biensChunked; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pageIndex => $chunk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="page">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pageIndex === 0 && $emplacement): ?>
                
                <div style="grid-column: 1 / -1; text-align: center; padding: 2mm; font-size: 10pt; font-weight: bold; border-bottom: 1px solid #ddd; margin-bottom: 1mm;">
                    <div>Emplacement: <?php echo e($emplacement->Emplacement ?? 'N/A'); ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($emplacement->localisation): ?>
                        <div style="font-size: 8pt; font-weight: normal; margin-top: 1mm;">
                            Localisation: <?php echo e($emplacement->localisation->Localisation ?? 'N/A'); ?>

                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($emplacement->affectation): ?>
                        <div style="font-size: 8pt; font-weight: normal;">
                            Affectation: <?php echo e($emplacement->affectation->Affectation ?? 'N/A'); ?>

                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div style="font-size: 7pt; font-weight: normal; margin-top: 1mm; color: #666;">
                        Total: <?php echo e($biens->count()); ?> immobilisation(s) | Page <?php echo e($pageIndex + 1); ?> / <?php echo e($biensChunked->count()); ?>

                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $chunk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bien): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="etiquette">
                    
                    <div class="barcode-wrapper">
                        <div class="barcode-container">
                            <?php
                                // Le code-barres est généré côté client, on affiche juste le code formaté
                                // Pour l'impression PDF, on génère un SVG simple ou on utilise le code formaté
                                $codeFormate = $bien->code_formate ?? $bien->NumOrdre;
                                
                                // Si le bien a un code-barres stocké, on l'utilise
                                if ($bien->code && $bien->code->barcode) {
                                    $barcodeData = $bien->code->barcode;
                                    
                                    // Vérifier le format
                                    if (str_starts_with($barcodeData, '<div') || str_starts_with($barcodeData, '<table')) {
                                        $htmlContent = $barcodeData;
                                    } elseif (str_starts_with($barcodeData, '<svg') || str_starts_with($barcodeData, '<?xml')) {
                                        $svgContent = $barcodeData;
                                        $svgContent = preg_replace('/width="[^"]*"/', '', $svgContent);
                                        $svgContent = preg_replace('/height="[^"]*"/', '', $svgContent);
                                        if (!str_contains($svgContent, 'style=')) {
                                            $svgContent = preg_replace('/<svg/', '<svg style="width: 100%; height: auto; max-width: 100%; display: block;"', $svgContent, 1);
                                        }
                                    } elseif (str_contains($barcodeData, '/') && !str_starts_with($barcodeData, '/9j/') && !str_starts_with($barcodeData, 'iVBORw0KGgo')) {
                                        $imagePath = public_path('storage/' . $barcodeData);
                                    } else {
                                        $base64Image = $barcodeData;
                                    }
                                } else {
                                    // Pas de code-barres stocké, on affiche juste le code
                                    $noBarcode = true;
                                }
                            ?>
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($htmlContent)): ?>
                                <div style="width: 100%; text-align: center; overflow: hidden;">
                                    <?php echo $htmlContent; ?>

                                </div>
                            <?php elseif(isset($svgContent)): ?>
                                <div style="width: 100%; display: flex; align-items: center; justify-content: center;">
                                    <?php echo $svgContent; ?>

                                </div>
                            <?php elseif(isset($imagePath) && file_exists($imagePath)): ?>
                                <img src="<?php echo e($imagePath); ?>" alt="Code-barres Code 128" style="width: 100%; height: auto; max-height: 100%; object-fit: contain; display: block;">
                            <?php elseif(isset($base64Image)): ?>
                                <img src="data:image/png;base64,<?php echo e($base64Image); ?>" alt="Code-barres Code 128" style="width: 100%; height: auto; max-height: 100%; object-fit: contain; display: block;">
                            <?php else: ?>
                                <div style="text-align: center; color: #999; font-size: 8pt; padding: 2mm;">
                                    Code-barres à générer
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                    
                    
                    <div class="code-text">
                        <?php echo e($codeFormate); ?>

                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = $chunk->count(); $i < 21; $i++): ?>
                <div class="etiquette" style="border: 0.5px dashed #eee; background: #f9f9f9;">
                    <div style="text-align: center; color: #ccc; font-size: 7pt;">
                        Vide
                    </div>
                </div>
            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views\pdf\etiquettes-biens-par-emplacement.blade.php ENDPATH**/ ?>