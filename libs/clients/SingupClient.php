<?php
    namespace PandaStore\Clients;

    require_once __DIR__ . '/../../vendor/autoload.php';
    require_once __DIR__.'/../../util.php';
    require_once __DIR__.'/BaseClient.php';
    require_once __DIR__.'/../util/Encrypter.php';


    use PandaStore\Util\Encrypter;
    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use Monolog\Formatter\LineFormatter;
    use nusoap;

    $formatter = new LineFormatter(null, null, true, true);
    $handler = new StreamHandler(__DIR__."/../../logs/".basename(__FILE__).".log", Logger::DEBUG);
    $handler->setFormatter($formatter);

    
    $log = new Logger(basename(__FILE__));
    $log->pushHandler($handler);


    class SingupClient extends BaseClient {
        private $singupServiceUrl;
        function __construct($singupServiceUrl) {
            parent::__construct($singupServiceUrl);
            $this->singupServiceUrl = $singupServiceUrl;
        }

        function singup($email, $rawPasswd, $givenName, $surname) {
            $passwd = Encrypter::encrypt($rawPasswd);
            return $this->call("createUser",
                ["email" => $email,
                    "passwd" => $passwd,
                    "givenName" => $givenName,
                    "surname" => $surname], 
                $this->singupServiceUrl
            );
        }
    }
