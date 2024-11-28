<?php

function WpalsLoad($class_name)
{
    $array_paths = array(
        "inc/class",
    );

    foreach ($array_paths as $path) {
        $file = sprintf(__DIR__.'/%s/%s.class.php', $path, $class_name);
        if (is_file($file)) {
            include_once $file;
        }
    }
}
spl_autoload_register('WpalsLoad');