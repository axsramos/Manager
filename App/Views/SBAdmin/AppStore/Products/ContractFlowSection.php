<?php
$features = $product['features'] ?? [];
?>
<section class="app-store-product-section">
    <div class="app-store-section-grid">
        <div>
            <p class="app-store-eyebrow">Recursos principais</p>
            <h3>Controle contratos ativos, franquias e consumo em uma única rotina</h3>
            <p class="text-muted">O ContractFlow organiza contratos por repositório, acompanha itens contratados e registra consumos com rastreabilidade para equipes operacionais e financeiras.</p>
        </div>
        <div class="app-store-feature-list">
            <?php foreach ($features as $feature): ?>
                <div class="app-store-feature">
                    <i class="fas fa-check"></i>
                    <span><?= app_store_e($feature); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="app-store-use-cases">
        <div>
            <i class="fas fa-file-signature"></i>
            <h4>Contratos e aditivos</h4>
            <p>Modele contratos, alterações e condições comerciais com histórico por repositório.</p>
        </div>
        <div>
            <i class="fas fa-balance-scale"></i>
            <h4>Franquias e saldos</h4>
            <p>Acompanhe limites contratados, consumo e estornos com visão operacional clara.</p>
        </div>
        <div>
            <i class="fas fa-clipboard-list"></i>
            <h4>Auditoria de consumo</h4>
            <p>Mantenha um extrato append-only para reduzir divergências e apoiar conferências.</p>
        </div>
    </div>
</section>
