<?php
    namespace PandaStore\Clients;

    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../Url.php";
    require_once __DIR__."/BaseClient.php";

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use Monolog\Formatter\LineFormatter;

    $formatter = new LineFormatter(null, null, true, true);
    $handler = new StreamHandler(__DIR__."/../../logs/".basename(__FILE__).".log", Logger::DEBUG);
    $handler->setFormatter($formatter);

    $log = new Logger(basename(__FILE__));
    $log->pushHandler($handler);


    class DbClient extends BaseClient {
        function __construct($dbServiceUrl) {
            parent::__construct($dbServiceUrl);
            $formatter = new LineFormatter(null, null, true, true);
            $handler = new StreamHandler(__DIR__."/../../logs/".basename(__FILE__).".log", Logger::DEBUG);
            $handler->setFormatter($formatter);
            $this->log = new Logger(basename(__FILE__));
            $this->log->pushHandler($handler);
        }

        public function send($to, $subject="No replay", $bodyTemplate=null, $parameters=null) {
            
        }
    }
