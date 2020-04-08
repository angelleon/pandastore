<?php
    //namespace PandaStore\Servers;
    require_once __DIR__ . '/../../vendor/autoload.php';
    require_once __DIR__.'/../../util.php';
    require_once __DIR__."/BaseServer.php";
    require_once __DIR__."/../types/DbStatement.php";
    require_once __DIR__."/../clients/DbClient.php";

    use PandaStore\Types\DbStatement;
    use PandaStore\Util\Encrypter;
    use PandaStore\Clients\DbClient;
    use PandaStore\Servers\BaseServer;

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    $log = new Logger(basename(__FILE__));
    $log->pushHandler(new StreamHandler(__DIR__."/../logs/".basename(__FILE__).".log", Logger::DEBUG));

    $SERVICE_URL = "$SERVICES_URL/".basename(__FILE__);

    function checkLogin($email, $passwd) {
        global $DB_SERVICE_URL; 
        $statement = (new DbStatement)
            ->column("userId")
            ->from("User")
            ->where("email")->eq($email)
            ->and("passwd")->eq(Encrypter::encrypt($passwd));
        $dbCleint = new DbClient($DB_SERVICE_URL);
        $ret = $dbCleint->select($statement);
        return ($ret["code"] == 0 ? ["code" => 0, "msg" => "successful loged in"] : ["code" => -1, "msg" => "Invalid login data"]);
    }

    class LoginServer extends BaseServer {
        private $loginServiceUrl;
        function __construct($loginServiceUrl) {
            parent::__construct($loginServiceUrl);
            $this->loginServiceUrl = $loginServiceUrl;
            $this->register('checkLogin',
                      ['email' => 'xsd:string',
                            'passwd' => 'xsd:string'],
                      ['return' => 'xsd:string'],
                      $loginServiceUrl
            );
        }
    }
