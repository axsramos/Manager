<?php
$product = $data['CurrentProduct'] ?? null;

if (! function_exists('app_store_e')) {
    function app_store_e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
?>
<section class="app-store-product">
    <?php if ($product === null): ?>
        <div class="app-store-empty">
            <i class="fas fa-search fa-3x mb-3"></i>
            <h3>Produto não encontrado</h3>
            <p class="text-muted">Verifique se o produto continua publicado no catálogo.</p>
            <a class="btn btn-primary" href="/Manager/AppStore">Voltar para a loja</a>
        </div>
    <?php else: ?>
        <div class="app-store-product-hero">
            <div class="app-store-product-copy">
                <div class="app-store-product-tags">
                    <span class="app-store-tag"><?= app_store_e($product['tag'] ?? 'Produto'); ?></span>
                    <?php if (! empty($product['badge'])): ?>
                        <span class="app-store-badge"><?= app_store_e($product['badge']); ?></span>
                    <?php endif; ?>
                    <?php if (! empty($product['category'])): ?>
                        <span class="app-store-category"><?= app_store_e($product['category']); ?></span>
                    <?php endif; ?>
                </div>
                <h2><?= app_store_e($product['title'] ?? 'Produto'); ?></h2>
                <p class="app-store-summary"><?= app_store_e($product['summary'] ?? $product['description'] ?? ''); ?></p>
                <div class="app-store-commercial">
                    <div>
                        <span>Valor</span>
                        <strong><?= app_store_e($product['price'] ?? 'Sob consulta'); ?></strong>
                    </div>
                    <div>
                        <span>Prazo</span>
                        <strong><?= app_store_e($product['term'] ?? 'Sob consulta'); ?></strong>
                    </div>
                </div>
                <?php include App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/AppStore/Partials/AppStoreAction.php'; ?>
            </div>
            <div class="app-store-product-visual">
                <img src="<?= app_store_e($product['cover_image'] ?? ''); ?>" alt="<?= app_store_e($product['title'] ?? 'Produto'); ?>" onerror="this.hidden=true; this.nextElementSibling.hidden=false;">
                <div class="app-store-visual-fallback" hidden>
                    <i class="<?= app_store_e($product['icon'] ?? 'fas fa-cube'); ?>"></i>
                    <span><?= app_store_e($product['title'] ?? 'Produto'); ?></span>
                </div>
            </div>
        </div>

        <?php
        $detailsView = (string) ($product['details_view'] ?? '');
        $defaultView = App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/AppStore/Products/DefaultProductSection.php';
        $viewPath = App\Core\Config::$DIR_BASE . '/' . ltrim(str_replace('\\', '/', $detailsView), '/');
        $basePath = realpath(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/AppStore/Products');
        $realViewPath = realpath($viewPath);

        if ($realViewPath !== false && $basePath !== false && str_starts_with($realViewPath, $basePath . DIRECTORY_SEPARATOR) && is_file($realViewPath)) {
            include $realViewPath;
        } else {
            include $defaultView;
        }
        ?>
    <?php endif; ?>
</section>
