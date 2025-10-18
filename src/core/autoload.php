<?php
spl_autoload_register(function ($className) {
    // Remove o namespace base
    $className = str_replace('app\\', '', $className);
    
    // Converte namespace para caminho de arquivo
    $file = __DIR__ . '/../' . str_replace('\\', '/', $className) . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});