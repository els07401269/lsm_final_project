<?php

/*
ENV HELPER CLASS
- Ginagamit para kunin ang values mula sa .env file o environment variables
- Para hindi hard-coded ang sensitive data (like DB credentials, API keys)
*/
class EnvHelper {

    /*
    GET ENV VALUE
    - Kinukuha ang value base sa key (hal. DB_HOST, APP_NAME)
    
    */
    public static function get($key, $default = null) {
        return $_ENV[$key] ?? $default;
    }
}