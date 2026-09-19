<?php

// PSR-4 simples: Api\Core\Router => Core/Router.php
// O nome de pastas e arquivos precisa bater EXATAMENTE com namespace/classe (Linux diferencia maiúsculas).
spl_autoload_register(function (string $class): void {
    $prefix = 'Api\\';

    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/' . str_replace('\\', '/', $relative) . '.php';

    if (is_file($file)) {
        require $file;
    }
});
