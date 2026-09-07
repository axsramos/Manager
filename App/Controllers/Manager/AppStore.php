<?php

namespace App\Controllers\Manager;

use App\Class\Manager\ApplicationActivationService;
use App\Class\Manager\ApplicationCatalogService;
use App\Class\Pattern\FormDesign;
use App\Core\AuthSession;
use App\Core\Config;
use App\Core\Controller;
use App\Shared\MessageDictionary;
use JsonException;

class AppStore extends Controller
{
    private MessageDictionary $dictionary;
    private ApplicationCatalogService $catalogService;
    private array $messages = [];
    private array $catalog = [];

    public function __construct()
    {
        if ((AuthSession::get()['USR_LOGGED'] ?? 'anonymous') === 'anonymous' || empty(AuthSession::get()['RPS_ID'])) {
            header('Location: /Home/Denied');
            exit();
        }

        $this->dictionary = new MessageDictionary();
        $this->catalogService = new ApplicationCatalogService();
        $this->catalog = $this->loadCatalog();
    }

    public function index(): void
    {
        $data = [
            'FormDesign' => $this->design('Loja de Aplicativos', 'Manager > Loja de Aplicativos', 0, 'AppStoreCatalog.php'),
            'Catalog' => $this->catalog,
            'Products' => $this->publishedProducts(),
            'CurrentProduct' => null,
        ];

        $this->view('SBAdmin/AppStore/AppStoreView', $data);
    }

    public function product(string $slug = ''): void
    {
        $product = $this->findProduct($slug);

        if ($product === null) {
            $this->messages[] = $this->dictionary->getMessage(2, 'Loja de Aplicativos', 'Produto não encontrado no catálogo.');
            $data = [
                'FormDesign' => $this->design('Loja de Aplicativos', 'Manager > Loja de Aplicativos', 1, 'AppStoreProduct.php'),
                'Catalog' => $this->catalog,
                'Products' => $this->publishedProducts(),
                'CurrentProduct' => null,
            ];
            $this->view('SBAdmin/AppStore/AppStoreView', $data);
            return;
        }

        $data = [
            'FormDesign' => $this->design((string) $product['title'], 'Manager > Loja de Aplicativos > Produto', 1, 'AppStoreProduct.php'),
            'Catalog' => $this->catalog,
            'Products' => $this->publishedProducts(),
            'CurrentProduct' => $product,
        ];

        $this->view('SBAdmin/AppStore/AppStoreView', $data);
    }

    public function activate(string $slug = ''): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /Manager/AppStore/Product/' . rawurlencode($slug));
            exit();
        }

        $token = trim((string) ($_POST['activation_token'] ?? ''));
        $service = new ApplicationActivationService();
        $result = $service->activate($slug, $token);

        if (($result['status'] ?? 'error') === 'applied' && ! empty($result['success_url'])) {
            header('Location: ' . $result['success_url']);
            exit();
        }

        $messageType = (($result['status'] ?? 'error') === 'error') ? 2 : 0;
        $this->messages[] = $this->dictionary->getMessage(
            $messageType,
            'Loja de Aplicativos',
            (string) ($result['message'] ?? 'Processamento concluído.')
        );

        $this->product($slug);
    }

    private function design(string $program, string $crumbs, int $current, string $loadFile): array
    {
        $design = FormDesign::withTabs('Portal SITI', $program, $crumbs, $this->getUserMenu(), $this->getSideMenu());
        $design['Tabs']['Items'] = [
            ['Name' => 'Catálogo', 'Link' => '/Manager/AppStore', 'IsDisabled' => false],
            ['Name' => 'Produto', 'Link' => '#', 'IsDisabled' => $current !== 1],
        ];
        $design['Tabs']['Current'] = $current;
        $design['Tabs']['LoadFile'] = 'App/Views/SBAdmin/AppStore/' . $loadFile;
        $design['Styles']['CSSFiles'] = ['/SBAdmin/css/app-store.css'];
        $design['Scripts']['Body'] = ['tooltip'];

        if ($this->messages !== []) {
            $design['Message'] = $this->messages[0];
        }

        return $design;
    }

    private function loadCatalog(): array
    {
        try {
            return $this->catalogService->load();
        } catch (\RuntimeException $exception) {
            $this->messages[] = $this->dictionary->getMessage(2, 'Loja de Aplicativos', $exception->getMessage());
            return ['version' => '1.0.0', 'items' => []];
        }

        $path = Config::$DIR_BASE . '/App/Static/Menu/AppStore.json';

        if (! is_file($path)) {
            $this->messages[] = $this->dictionary->getMessage(2, 'Loja de Aplicativos', 'Catálogo da loja não localizado.');
            return ['version' => '1.0.0', 'items' => []];
        }

        $content = file_get_contents($path);
        if ($content === false) {
            $this->messages[] = $this->dictionary->getMessage(2, 'Loja de Aplicativos', 'Não foi possível ler o catálogo da loja.');
            return ['version' => '1.0.0', 'items' => []];
        }

        try {
            $catalog = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            $this->messages[] = $this->dictionary->getMessage(2, 'Loja de Aplicativos', 'Catálogo da loja com JSON inválido.');
            return ['version' => '1.0.0', 'items' => []];
        }

        if (! is_array($catalog) || ! isset($catalog['items']) || ! is_array($catalog['items'])) {
            $this->messages[] = $this->dictionary->getMessage(2, 'Loja de Aplicativos', 'Catálogo da loja com estrutura inválida.');
            return ['version' => '1.0.0', 'items' => []];
        }

        return $catalog;
    }

    private function publishedProducts(): array
    {
        try {
            return $this->catalogService->publishedProducts();
        } catch (\RuntimeException $exception) {
            return [];
        }

        $products = array_values(array_filter(
            $this->catalog['items'] ?? [],
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

    private function findProduct(string $slug): ?array
    {
        try {
            return $this->catalogService->findProduct($slug);
        } catch (\RuntimeException $exception) {
            return null;
        }

        $slug = trim($slug);
        if ($slug === '') {
            return null;
        }

        foreach ($this->publishedProducts() as $product) {
            if (($product['slug'] ?? '') === $slug || ($product['app_id'] ?? '') === $slug) {
                return $product;
            }
        }

        return null;
    }
}
