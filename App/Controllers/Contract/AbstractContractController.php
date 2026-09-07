<?php

namespace App\Controllers\Contract;

use App\Class\Contract\ContractReadService;
use App\Class\Pattern\FormDesign;
use App\Core\AuthSession;
use App\Core\Controller;
use App\Shared\MessageDictionary;
use RuntimeException;

abstract class AbstractContractController extends Controller
{
    protected MessageDictionary $dictionary;
    protected ContractReadService $read;
    protected array $messages = [];

    protected function boot(string $program): void
    {
        $this->validateAccess($program);
        $this->dictionary = new MessageDictionary();
        $this->read = new ContractReadService();
    }

    protected function repositoryId(): string
    {
        $repositoryId = trim((string) (AuthSession::get()['RPS_ID'] ?? ''));
        if ($repositoryId === '') {
            throw new RuntimeException('Repositório ativo não localizado na sessão.');
        }
        $requested = trim((string) ($_POST['RepositoryId'] ?? $_GET['RepositoryId'] ?? $repositoryId));
        if ($requested !== $repositoryId) {
            throw new RuntimeException('A operação deve usar o repositório ativo da sessão.');
        }
        return $repositoryId;
    }

    protected function design(string $program, string $crumbs, array $tabs, int $current, string $loadFile): array
    {
        $design = FormDesign::withTabs('ContractFlow', $program, $crumbs, $this->getUserMenu(), $this->getSideMenu());
        $design['Tabs']['Items'] = $tabs;
        $design['Tabs']['Current'] = $current;
        $design['Tabs']['LoadFile'] = 'App/Views/SBAdmin/Contract/' . $loadFile;
        $design['TransMode'] = 'Default';
        if ($this->messages !== []) {
            $design['Message'] = $this->messages[0];
        }
        return $design;
    }

    protected function tab(string $name, string $link, bool $disabled = false): array
    {
        return ['Name' => $name, 'Link' => $link, 'IsDisabled' => $disabled];
    }

    protected function success(string $message): void
    {
        $this->messages[] = $this->dictionary->getMessage(0, 'ContractFlow', $message);
    }

    protected function error(\Throwable $exception): void
    {
        $this->messages[] = $this->dictionary->getMessage(1, 'ContractFlow', $exception->getMessage());
    }

    protected function value(string $field, ?string $default = null): ?string
    {
        if (!isset($_POST[$field])) { return $default; }
        $value = trim((string) $_POST[$field]);
        return $value === '' ? null : $value;
    }

    protected function integer(string $field, ?int $default = null): ?int
    {
        $value = $this->value($field);
        return $value === null ? $default : (int) $value;
    }

    protected function checkbox(string $field): int
    {
        return isset($_POST[$field]) ? 1 : 0;
    }

    protected function money(string $field): ?int
    {
        $value = $this->value($field);
        return $value === null ? null : (int) preg_replace('/[^0-9-]/', '', $value);
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . $path);
        exit;
    }
}
