<?php
$flash = consume_flash();
?>
<?php if (!empty($flash['success'])): ?>
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm flex items-center justify-between">
        <span><?= e($flash['success']) ?></span>
        <button type="button" onclick="this.parentElement.remove()" class="text-green-400 hover:text-green-700 font-bold">&times;</button>
    </div>
<?php endif; ?>
<?php if (!empty($flash['error'])): ?>
    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-center justify-between">
        <span><?= e($flash['error']) ?></span>
        <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-700 font-bold">&times;</button>
    </div>
<?php endif; ?>