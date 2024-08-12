<?php

class Singleton
{
    private static ?Singleton $instance = null;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    public static function getInstance(): Singleton
    {
        if (self::$instance === null) {
            self::$instance = new Singleton();
        }

        return self::$instance;
    }

    public function doSomething(): void
    {
        echo 'Doing something';
    }
}

$single = Singleton::getInstance();
$single->doSomething();
