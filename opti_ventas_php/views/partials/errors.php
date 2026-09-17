<?php
$allErrors = errors();
?>
<?php if ($allErrors): ?>
    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm">
        <?php if (count($allErrors) > 1): ?>
            <p class="font-semibold text-red-800 mb-1">Se encontraron los siguientes errores:</p>
        <?php else: ?>
            <p class="font-semibold text-red-800 mb-1">Se encontró el siguiente error:</p>
        <?php endif; ?>
        <ul class="list-disc list-inside space-y-1">
            <?php foreach ($allErrors as $field => $message): ?>
                <li class="text-red-700"><?= e($message) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>