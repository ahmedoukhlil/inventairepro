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
            font-family: Arial, sans-serif;
            width: 100mm;
            height: 80mm;
            padding: 8mm;
            border: 2px solid #000;
        }
        .qr-container {
            text-align: center;
            margin-bottom: 5mm;
        }
        .qr-code {
            width: 60mm;
            height: 60mm;
            display: block;
            margin: 0 auto;
        }
        .code {
            font-size: 18pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 3mm;
        }
        .designation {
            font-size: 14pt;
            text-align: center;
            margin-bottom: 2mm;
            line-height: 1.3;
        }
        .info {
            font-size: 12pt;
            text-align: center;
            margin-top: 3mm;
        }
        .batiment-etage {
            font-size: 11pt;
            text-align: center;
            color: #666;
            margin-top: 2mm;
        }
    </style>
</head>
<body>
    <div class="qr-container">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($localisation->qr_code_path && Storage::disk('public')->exists($localisation->qr_code_path)): ?>
            <img src="<?php echo e(public_path('storage/' . $localisation->qr_code_path)); ?>" alt="QR Code" class="qr-code">
        <?php else: ?>
            <div style="width: 60mm; height: 60mm; background: #f0f0f0; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                <span style="color: #999;">QR Code non disponible</span>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    
    <div class="code"><?php echo e($localisation->code); ?></div>
    
    <div class="designation"><?php echo e($localisation->designation); ?></div>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($localisation->batiment || $localisation->etage): ?>
        <div class="batiment-etage">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($localisation->batiment): ?>
                Bâtiment: <?php echo e($localisation->batiment); ?>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($localisation->batiment && $localisation->etage): ?>
                -
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($localisation->etage): ?>
                Étage: <?php echo e($localisation->etage); ?>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($localisation->service_rattache): ?>
        <div class="info">Service: <?php echo e($localisation->service_rattache); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</body>
</html>

<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views\pdf\etiquette-localisation.blade.php ENDPATH**/ ?>