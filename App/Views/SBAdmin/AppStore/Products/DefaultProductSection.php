<section class="app-store-product-section">
    <div class="app-store-section-grid">
        <div>
            <p class="app-store-eyebrow">Visão geral</p>
            <h3><?= app_store_e($product['title'] ?? 'Produto'); ?></h3>
            <p class="text-muted"><?= app_store_e($product['description'] ?? 'As informações detalhadas deste produto serão publicadas em breve.'); ?></p>
        </div>
        <div class="app-store-feature-list">
            <?php foreach (($product['features'] ?? []) as $feature): ?>
                <div class="app-store-feature">
                    <i class="fas fa-check"></i>
                    <span><?= app_store_e($feature); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
