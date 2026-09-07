<?php
if (! function_exists('app_store_action_config')) {
    function app_store_action_config(array $product): array
    {
        $mode = (string) ($product['activation_mode'] ?? 'external');

        return match ($mode) {
            'free' => ['label' => 'Ativar', 'class' => 'btn-success', 'icon' => 'fas fa-bolt', 'disabled' => false],
            'token' => ['label' => 'Informar chave', 'class' => 'btn-primary', 'icon' => 'fas fa-key', 'disabled' => false],
            'contract' => ['label' => 'Ativar', 'class' => 'btn-success', 'icon' => 'fas fa-check-circle', 'disabled' => false],
            'trial' => ['label' => 'Iniciar teste', 'class' => 'btn-info', 'icon' => 'fas fa-hourglass-start', 'disabled' => false],
            'active' => ['label' => 'Abrir', 'class' => 'btn-outline-primary', 'icon' => 'fas fa-external-link-alt', 'disabled' => false],
            'update' => ['label' => 'Atualizar', 'class' => 'btn-warning', 'icon' => 'fas fa-sync-alt', 'disabled' => false],
            'unavailable' => ['label' => 'Indisponível', 'class' => 'btn-secondary', 'icon' => 'fas fa-ban', 'disabled' => true],
            default => ['label' => 'Solicitar contratação', 'class' => 'btn-outline-primary', 'icon' => 'fas fa-comment-dollar', 'disabled' => false],
        };
    }
}

if (! function_exists('app_store_e')) {
    function app_store_e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

$action = app_store_action_config($product);
$learnMoreUrl = (string) ($product['learn_more_url'] ?? ('/Manager/AppStore/Product/' . ($product['slug'] ?? '')));
$isProductPage = ($data['CurrentProduct'] ?? null) !== null;
$actionUrl = $isProductPage ? '#' : $learnMoreUrl;
$activationMode = (string) ($product['activation_mode'] ?? 'external');
$activationUrl = '/Manager/AppStore/activate/' . rawurlencode((string) ($product['slug'] ?? $product['app_id'] ?? $product['product_key'] ?? ''));
?>
<div class="app-store-actions">
    <?php if ($isProductPage): ?>
        <form class="app-store-activation-form" method="post" action="<?= app_store_e($activationUrl); ?>">
            <?php if ($activationMode === 'token'): ?>
                <label class="sr-only" for="activation_token_<?= app_store_e($product['slug'] ?? 'product'); ?>">Chave de ativação</label>
                <input class="form-control" id="activation_token_<?= app_store_e($product['slug'] ?? 'product'); ?>" name="activation_token" type="text" placeholder="Chave de ativação" autocomplete="off" required>
            <?php endif; ?>
            <button class="btn <?= app_store_e($action['class']); ?>" type="submit" <?= $action['disabled'] ? 'disabled' : ''; ?>>
                <i class="<?= app_store_e($action['icon']); ?> mr-1"></i><?= app_store_e($action['label']); ?>
            </button>
        </form>
    <?php else: ?>
        <a class="btn <?= app_store_e($action['class']); ?> <?= $action['disabled'] ? 'disabled' : ''; ?>" href="<?= app_store_e($actionUrl); ?>" aria-disabled="<?= $action['disabled'] ? 'true' : 'false'; ?>">
            <i class="<?= app_store_e($action['icon']); ?> mr-1"></i><?= app_store_e($action['label']); ?>
        </a>
        <a class="btn btn-link" href="<?= app_store_e($learnMoreUrl); ?>">Detalhes</a>
    <?php endif; ?>
    <?php if ($isProductPage): ?>
        <a class="btn btn-link" href="/Manager/AppStore">Voltar para a loja</a>
    <?php endif; ?>
</div>
