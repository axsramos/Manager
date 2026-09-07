<?php
if (! function_exists('app_store_e')) {
    function app_store_e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
?>
<article class="app-store-card">
    <a class="app-store-card-image" href="<?= app_store_e($product['learn_more_url'] ?? '#'); ?>" aria-label="Ver detalhes de <?= app_store_e($product['title'] ?? 'produto'); ?>">
        <img src="<?= app_store_e($product['cover_image'] ?? ''); ?>" alt="<?= app_store_e($product['title'] ?? 'Produto'); ?>" onerror="this.hidden=true; this.nextElementSibling.hidden=false;">
        <div class="app-store-visual-fallback" hidden>
            <i class="<?= app_store_e($product['icon'] ?? 'fas fa-cube'); ?>"></i>
            <span><?= app_store_e($product['title'] ?? 'Produto'); ?></span>
        </div>
    </a>
    <div class="app-store-card-body">
        <div class="app-store-card-meta">
            <span class="app-store-tag"><?= app_store_e($product['tag'] ?? 'Produto'); ?></span>
            <?php if (! empty($product['badge'])): ?>
                <span class="app-store-badge"><?= app_store_e($product['badge']); ?></span>
            <?php endif; ?>
        </div>
        <h3><a href="<?= app_store_e($product['learn_more_url'] ?? '#'); ?>"><?= app_store_e($product['title'] ?? 'Produto'); ?></a></h3>
        <p><?= app_store_e($product['description'] ?? ''); ?></p>
        <div class="app-store-card-facts">
            <span><i class="fas fa-layer-group"></i><?= app_store_e($product['category'] ?? 'Aplicativo'); ?></span>
            <span><i class="far fa-clock"></i><?= app_store_e($product['term'] ?? 'Sob consulta'); ?></span>
        </div>
        <div class="app-store-card-price">
            <span>Valor</span>
            <strong><?= app_store_e($product['price'] ?? 'Sob consulta'); ?></strong>
        </div>
        <?php include App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/AppStore/Partials/AppStoreAction.php'; ?>
    </div>
</article>
