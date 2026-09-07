<?php
if (! function_exists('app_store_e')) {
    function app_store_e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
?>
<div class="table-responsive app-store-list">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th scope="col">Produto</th>
                <th scope="col">Categoria</th>
                <th scope="col">Tag</th>
                <th scope="col">Valor</th>
                <th scope="col">Prazo</th>
                <th scope="col">Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td>
                        <div class="app-store-list-product">
                            <span class="app-store-list-icon"><i class="<?= app_store_e($product['icon'] ?? 'fas fa-cube'); ?>"></i></span>
                            <div>
                                <a href="<?= app_store_e($product['learn_more_url'] ?? '#'); ?>"><?= app_store_e($product['title'] ?? 'Produto'); ?></a>
                                <small><?= app_store_e($product['summary'] ?? $product['description'] ?? ''); ?></small>
                            </div>
                        </div>
                    </td>
                    <td><?= app_store_e($product['category'] ?? 'Aplicativo'); ?></td>
                    <td><span class="app-store-tag"><?= app_store_e($product['tag'] ?? 'Produto'); ?></span></td>
                    <td><?= app_store_e($product['price'] ?? 'Sob consulta'); ?></td>
                    <td><?= app_store_e($product['term'] ?? 'Sob consulta'); ?></td>
                    <td><?php include App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/AppStore/Partials/AppStoreAction.php'; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
