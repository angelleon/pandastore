<?php
    //namespace PandaStore\Servers;
    require_once __DIR__.'/../vendor/autoload.php';
    require_once __DIR__.'/../Url.php';
    require_once __DIR__."/BaseServer.php";
    require_once __DIR__."/../types/dto/StmntBuilder.php";
    require_once __DIR__."/../clients/DbClient.php";

    use PandaStore\Types\Dto\StmntBuilder;
    use PandaStore\Clients\DbClient;
    use PandaStore\Servers\BaseServer;

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use Monolog\Formatter\LineFormatter;

    
    $formatter = new LineFormatter(null, null, true, true);
    $handler = new StreamHandler(__DIR__."/../../logs/".basename(__FILE__).".log", Logger::DEBUG);
    $handler->setFormatter($formatter);

    $log = new Logger(basename(__FILE__));
    $log->pushHandler($handler);

    
    function createUser($email, $passwd, $givenName, $surname) {
        global $log;
        $statement = StmntBuilder::insert("email")
                ->column("passwd")
                ->column("givenName")
                ->column("surname")
                ->into("User")
                ->value($email)
                ->value($passwd)
                ->value($givenName)
                ->value($surname)
                ->build();
        $dbClient = new DbClient(DB_SERVICE_URL);
        $log->debug("executing insert statement");
        $result = $dbClient->insert($statement);
        $log->debug("insert result: ".json_encode($result, JSON_PRETTY_PRINT));
        if ($result === false) {
            return ['code' => -1, 'msg' => 'db service uanbailable'];
        } else if (is_array($result) && 
            array_key_exists("code", $result) && 
            array_key_exists('msg', $result)) {
            return $result;
        } else {
            return ['code' => -2, 'msg' => 'Unknown error'];
        }
    }

    class SingupServer extends BaseServer {
        private $singupServiceUrl;
        function __construct($singupServiceUrl) {
            parent::__construct($singupServiceUrl);
            $this->$singupServiceUrl = $singupServiceUrl;
            $this->register('createUser',
                             ['email' => 'xsd:string',
                                'passwd' => 'xsd:string',
                                'givenName' => 'xsd:string',
                                'surname' => 'xsd:string'],
                             ['return' => "tns:stdMessage"],
                            $this->singupServiceUrl);
        }
    }