<?php

namespace Api\Core;

use Api\Config\Env;
use PDOException;
use ReflectionMethod;
use Throwable;

class Router
{
    private array $routes = [];
    private array $groupMiddleware = [];

    /** Registra rotas dentro do callback com os middlewares informados. */
    public function group(array $middleware, callable $callback): void
    {
        $previous = $this->groupMiddleware;
        $this->groupMiddleware = array_merge($previous, $middleware);
        $callback($this);
        $this->groupMiddleware = $previous;
    }

    /**
     * Parâmetros: {id} casa só números; {slug:[a-z-]+} permite regex própria.
     * Handler: [Controller::class, 'metodo'] ou closure.
     */
    public function addRoute(string $method, string $path, array|callable $handler, array $middleware = []): void
    {
        $regex = preg_replace_callback(
            '/\{(\w+)(?::([^}]+))?\}/',
            fn (array $m) => '(?P<' . $m[1] . '>' . ($m[2] ?? '\d+') . ')',
            $path
        );

        $this->routes[] = [
            'method'     => strtoupper($method),
            'regex'      => '#^' . $regex . '$#',
            'handler'    => $handler,
            'middleware' => array_merge($this->groupMiddleware, $middleware),
        ];
    }

    public function get(string $path, array|callable $handler, array $mw = []): void    { $this->addRoute('GET', $path, $handler, $mw); }
    public function post(string $path, array|callable $handler, array $mw = []): void   { $this->addRoute('POST', $path, $handler, $mw); }
    public function put(string $path, array|callable $handler, array $mw = []): void    { $this->addRoute('PUT', $path, $handler, $mw); }
    public function patch(string $path, array|callable $handler, array $mw = []): void   { $this->addRoute('PATCH', $path, $handler, $mw); }
    public function delete(string $path, array|callable $handler, array $mw = []): void  { $this->addRoute('DELETE', $path, $handler, $mw); }

    public function dispatch(string $method, string $path): void
    {
        try {
            $allowed = [];

            foreach ($this->routes as $route) {
                if (!preg_match($route['regex'], $path, $matches)) {
                    continue;
                }

                if ($route['method'] !== $method) {
                    $allowed[] = $route['method'];
                    continue;
                }

                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[] = ctype_digit($value) ? (int) $value : $value;
                    }
                }

                $this->run($route, $params);
                return;
            }

            if ($allowed) {
                header('Allow: ' . implode(', ', array_unique($allowed)));
                throw new HttpException('Método não permitido.', 405);
            }

            throw new HttpException('Rota não encontrada.', 404);
        } catch (HttpException $e) {
            Response::error($e->getMessage(), $e->getStatus(), $e->getDetails());
        } catch (PDOException $e) {
            error_log('PDO: ' . $e->getMessage());
            // 23000 = violação de integridade (duplicado, FK inexistente, FK em uso...)
            if ($e->getCode() === '23000') {
                Response::error('Operação viola uma regra de integridade (registro duplicado, inexistente ou em uso).', 409);
                return;
            }
            $this->internalError($e);
        } catch (Throwable $e) {
            error_log($e);
            $this->internalError($e);
        }
    }

    private function run(array $route, array $params): void
    {
        foreach ($route['middleware'] as $middleware) {
            (new $middleware())->handle();
        }

        $handler = $route['handler'];

        if (is_array($handler) && is_string($handler[0])) {
            $controller = new $handler[0]();
            $action = $handler[1];

            // Se o método aceita mais um argumento além dos da URL, ele recebe o corpo JSON
            if ((new ReflectionMethod($controller, $action))->getNumberOfParameters() > count($params)) {
                $params[] = Request::getBody();
            }

            $controller->$action(...$params);
            return;
        }

        $handler(...$params);
    }

    private function internalError(Throwable $e): void
    {
        $debug = filter_var(Env::get('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN);
        Response::error($debug ? $e->getMessage() : 'Erro interno do servidor.', 500);
    }
}
