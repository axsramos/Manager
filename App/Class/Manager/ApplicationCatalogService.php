<?php

namespace App\Class\Manager;

use App\Core\Config;
use JsonException;
use RuntimeException;

class ApplicationCatalogService
{
    private const DEFAULT_CATALOG = ['version' => '1.0.0', 'items' => []];

    public function load(): array
    {
        $path = Config::$DIR_BASE . '/App/Static/Menu/AppStore.json';

        if (! is_file($path)) {
            throw new RuntimeException('Catálogo da loja não localizado.');
        }

        $content = file_get_contents($path);
        if ($content === false) {
            throw new RuntimeException('Não foi possível ler o catálogo da loja.');
        }

        try {
            $catalog = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('Catálogo da loja com JSON inválido.', 0, $exception);
        }

        if (! is_array($catalog) || ! isset($catalog['items']) || ! is_array($catalog['items'])) {
            throw new RuntimeException('Catálogo da loja com estrutura inválida.');
        }

        return array_merge(self::DEFAULT_CATALOG, $catalog);
    }

    public function publishedProducts(): array
    {
        $products = array_values(array_filter(
            $this->load()['items'] ?? [],
            fn (array $item): bool => ($item['status'] ?? 'published') === 'published'
        ));

        usort($products, function (array $a, array $b): int {
            $group = strcmp((string) ($a['sort_group'] ?? ''), (string) ($b['sort_group'] ?? ''));
            if ($group !== 0) {
                return $group;
            }

            return ((int) ($a['order'] ?? 9999)) <=> ((int) ($b['order'] ?? 9999));
        });

        return $products;
    }

    public function findProduct(string $identifier): ?array
    {
        $identifier = trim($identifier);
        if ($identifier === '') {
            return null;
        }

        foreach ($this->publishedProducts() as $product) {
            if (($product['slug'] ?? '') === $identifier
                || ($product['app_id'] ?? '') === $identifier
                || ($product['product_key'] ?? '') === $identifier) {
                return $product;
            }
        }

        return null;
    }
}
