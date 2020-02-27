<?php
    require __DIR__."/vendor/autoload.php";

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;

    $log = new Logger("testmono");
    $log->pushHandler(new StreamHandler("mono.log", Logger::DEBUG));

    $log->debug("hello world");
    