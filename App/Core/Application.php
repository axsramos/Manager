<?php

namespace App\Core;

class Application
{
    protected object $controller;
    protected string $method;
    protected bool $page404;
    protected array $parameters;
    private int $idxUrl;
    private string $version;
    private array $groups;
    private string $group;

    public function __construct()
    {
        $this->method = Config::$HOMEPAGE_METHOD;
        $this->page404 = false;
        $this->parameters = array();
        $this->idxUrl = 0;
        $this->version = '';
        $this->groups = Config::getPageGroup();
        $this->group = "\\";

        AuthSession::create();

        $this->run();
    }

    private function run(): void
    {
        $urlParameters = $this->parseUrl();

        $this->getVersion($urlParameters);
        $this->getGroup($urlParameters);
        $this->getControllerFromUrl($urlParameters);
        $this->getMethodFromUrl($urlParameters);
        $this->getParameterFromUrl($urlParameters);

        call_user_func_array([$this->controller, $this->method], $this->parameters);
    }

    private function parseUrl(): array
    {
        $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        if (! is_string($requestPath)) {
            $requestPath = '/';
        }

        return explode('/', ltrim($requestPath, '/'));
    }

    private function getVersion(array $url): void
    {
        // Confere se o primeiro caractere em $url[1] é 'v' e os seguintes são dígitos. //
        if (isset($url[1])) {
            if (preg_match('/^V\d+$/', $url[1])) {
                $this->version = "\\"  . trim(strtolower($url[1]));
                $this->idxUrl = 2;
            }
        }
    }

    private function getGroup(array $url): void
    {
        if (isset($url[$this->idxUrl])) {
            if (in_array(strtolower($url[$this->idxUrl]), $this->groups)) {
                $this->group = "\\"  . trim(ucfirst($url[$this->idxUrl])) . "\\";
                $this->idxUrl += 1;
            }
        }
    }

    private function getControllerFromUrl(array $url): void
    {
        /**
         * Por padrão rota é home
         * Quando rota for "/", assume home
         * Aceita rota CamelCase ou separador hífen. Ex: [HomePage | home-page | Home-Page]
         * O nome da rota é convertido para CamelCase e o controller sempre Camelcase. Ex: HomePage
         */
        if (isset($url[$this->idxUrl])) {
            $pageName = ucwords(str_replace('-', ' ', ucfirst($url[$this->idxUrl])));
            $pageName = str_replace(' ', '', $pageName);
        }

        if (empty($pageName)) {
            $pageName = ucfirst(Config::$HOMEPAGE);
        }

        if (empty($this->version)) {
            $className = "App\\Controllers" . $this->group . $pageName;
        } else {
            $className = 'App\\Controllers\\' . $url[0] . $this->version . $this->group . $pageName;
        }

        $fileResource = str_replace("\\", "/", $className) . ".php";

        if (! file_exists($fileResource)) {
            $this->page404 = true;

            $fileResource = "App/Controllers/" . Config::$PAGE_NOT_FOUND . ".php";
            $className = "App\\Controllers\\" . Config::$PAGE_NOT_FOUND . "";
        }

        require_once $fileResource;

        $this->controller = new $className();
    }

    private function getMethodFromUrl(array $url): void
    {
        $this->idxUrl += 1;

        if ($this->page404) {
            $this->setPageNotFoundMethod();
        } else {
            if (isset($url[$this->idxUrl]) && !empty($url[$this->idxUrl])) {
                $method = $url[$this->idxUrl];
                $parameters = array_slice($url, $this->idxUrl + 1);

                if ($this->isRoutableAction($this->controller, $method, $parameters)) {
                    $this->method = $method;
                } else {
                    $this->setPageNotFoundMethod();
                }
            }
        }
    }

    private function isRoutableAction(object $controller, string $method, array $parameters): bool
    {
        if (str_starts_with($method, '__') || ! method_exists($controller, $method)) {
            return false;
        }

        $reflection = new \ReflectionMethod($controller, $method);

        if (! $reflection->isPublic() || $reflection->isStatic()) {
            return false;
        }

        if ($reflection->getDeclaringClass()->getName() !== $controller::class) {
            return false;
        }

        $parameterCount = count($parameters);

        if ($parameterCount < $reflection->getNumberOfRequiredParameters()) {
            return false;
        }

        return $reflection->isVariadic()
            || $parameterCount <= $reflection->getNumberOfParameters();
    }

    private function setPageNotFoundMethod(): void
    {
        http_response_code(404);
        $this->page404 = true;
        $this->method = Config::$PAGE_NOT_FOUND_METHOD;
    }

    private function getParameterFromUrl(array $url): void
    {
        $this->idxUrl += 1;

        if ($this->page404) {
            $this->parameters = array();
            return;
        }

        if (count($url) > $this->idxUrl) {
            $this->parameters = array_slice($url, $this->idxUrl);
        }
    }
}
