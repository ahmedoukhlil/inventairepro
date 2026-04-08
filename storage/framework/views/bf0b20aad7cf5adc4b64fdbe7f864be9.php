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
            margin: 10mm;
            size: A4 portrait;
        }
        body {
            font-family: Arial, sans-serif;
        }
        .page {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(2, 1fr);
            gap: 5mm;
            page-break-after: always;
        }
        .etiquette {
            width: 100mm;
            height: 80mm;
            padding: 8mm;
            border: 2px solid #000;
            page-break-inside: avoid;
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
    <?php
        $localisationsChunked = $localisations->chunk(4);
    ?>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $localisationsChunked; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chunk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="page">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $chunk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $localisation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="etiquette">
                    <div class="qr-container">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($localisation->qr_code_path && Storage::disk('public')->exists($localisation->qr_code_path)): ?>
                            <img src="<?php echo e(public_path('storage/' . $localisation->qr_code_path)); ?>" alt="QR Code" class="qr-code">
                        <?php else: ?>
                            <div style="width: 60mm; height: 60mm; background: #f0f0f0; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                <span style="color: #999; font-size: 10pt;">QR Code non disponible</span>
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
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</body>
</html>

<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views\pdf\etiquettes-localisations.blade.php ENDPATH**/ ?>