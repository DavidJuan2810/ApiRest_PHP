<?php

spl_autoload_register(function ($className) {
    $file = __DIR__ . '/../controllers/' . $className . '.php';
    if (file_exists($file)) {
        require_once $file;
    };

});

?>
