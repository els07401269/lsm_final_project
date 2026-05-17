<?php

function loadEnv($path)
{
    if (!file_exists($path)) {
        die("❌ .env file not found");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {

        $line = trim($line);

        if ($line === '' || strpos($line, '#') === 0) continue;

        list($key, $value) = explode("=", $line, 2);

        $_ENV[$key] = trim($value);
        putenv("$key=$value");
    }
}

loadEnv(__DIR__ . '/.env');