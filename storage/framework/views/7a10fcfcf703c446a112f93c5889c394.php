<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Codes - Impression</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: white;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 5mm;
            padding: 5mm;
        }

        .qr-item {
            border: 2px solid #000;
            padding: 8mm;
            page-break-inside: avoid;
            text-align: center;
            background: white;
        }

        .qr-code {
            width: 70mm;
            height: 70mm;
            margin: 0 auto 5mm;
        }

        .qr-code svg {
            width: 100%;
            height: 100%;
        }

        .code {
            font-size: 20pt;
            font-weight: bold;
            margin-bottom: 2mm;
            color: #000;
        }

        .name {
            font-size: 12pt;
            font-weight: 600;
            margin-bottom: 3mm;
            color: #333;
        }

        .location {
            font-size: 10pt;
            color: #666;
            margin: 1mm 0;
        }

        .qr-data {
            font-family: 'Courier New', monospace;
            font-size: 10pt;
            background: #f0f0f0;
            padding: 2mm 4mm;
            margin-top: 3mm;
            display: inline-block;
            border: 1px solid #ddd;
        }

        @media print {
            body {
                background: white;
            }

            .grid {
                padding: 0;
            }

            .qr-item {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            @page {
                size: A4 portrait;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>
    <div class="grid">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $qrCodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="qr-item">
                <div class="qr-code">
                    <?php echo $item['qrCode']; ?>

                </div>

                <div class="code"><?php echo e($item['emplacement']->CodeEmplacement); ?></div>
                <div class="name"><?php echo e($item['emplacement']->Emplacement); ?></div>
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item['emplacement']->localisation): ?>
                    <div class="location"><?php echo e($item['emplacement']->localisation->Localisation); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item['emplacement']->affectation): ?>
                    <div class="location"><?php echo e($item['emplacement']->affectation->Affectation); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="qr-data"><?php echo e($item['qrData']); ?></div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <script>
        window.onload = () => window.print();
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views\qrcodes\emplacements-print.blade.php ENDPATH**/ ?>