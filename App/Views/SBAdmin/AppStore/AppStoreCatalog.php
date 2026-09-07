<?php
$products = $data['Products'] ?? [];
?>
<section class="app-store-catalog">
    <div class="app-store-heading">
        <div>
            <p class="app-store-eyebrow">Loja de aplicativos</p>
            <h2>Aplicativos disponíveis</h2>
            <p class="text-muted mb-0">Escolha os produtos para conhecer recursos, condições e próximos passos de ativação.</p>
        </div>
        <div class="btn-group btn-group-toggle app-store-view-toggle" role="group" aria-label="Alternar visualização">
            <button type="button" id="appStoreCardsButton" class="btn btn-outline-secondary active" aria-pressed="true" data-toggle="tooltip" title="Visualizar em cards">
                <i class="fas fa-th-large"></i>
            </button>
            <button type="button" id="appStoreListButton" class="btn btn-outline-secondary" aria-pressed="false" data-toggle="tooltip" title="Visualizar em lista">
                <i class="fas fa-list"></i>
            </button>
        </div>
    </div>

    <?php if ($products === []): ?>
        <div class="app-store-empty">
            <i class="fas fa-store fa-3x mb-3"></i>
            <h3>Nenhum aplicativo publicado</h3>
            <p class="text-muted mb-0">Os produtos publicados no catálogo serão exibidos aqui.</p>
        </div>
    <?php else: ?>
        <div id="appStoreCardsView" class="app-store-grid">
            <?php foreach ($products as $product): ?>
                <?php include App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/AppStore/Partials/AppStoreCard.php'; ?>
            <?php endforeach; ?>
        </div>

        <div id="appStoreListView" hidden>
            <?php include App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/AppStore/Partials/AppStoreList.php'; ?>
        </div>
    <?php endif; ?>
</section>
