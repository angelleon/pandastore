<?php
    //namespace PandaStore\Servers;
    require_once __DIR__ . '/../vendor/autoload.php';
    require_once __DIR__.'/../Url.php';
    require_once __DIR__."/BaseServer.php";
    require_once __DIR__."/../types/dto/StmntBuilder.php";
    require_once __DIR__."/../clients/DbClient.php";
    require_once __DIR__."/../util/Encrypter.php";

    use nusoap_base;

    use PandaStore\Types\Dto\StmntBuilder;
    use PandaStore\Util\Encrypter;
    use PandaStore\Clients\DbClient;
    use PandaStore\Servers\BaseServer;

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    $log = new Logger(basename(__FILE__));
    $log->pushHandler(new StreamHandler(__DIR__."/../../logs/".basename(__FILE__).".log", Logger::DEBUG));


    function checkLogin($email, $passwd) {
        global $log;
        $statement = StmntBuilder::select("idUser")
            ->from("User")
            ->where("email")->eq($email)
            ->and("passwd")->eq(Encrypter::encrypt($passwd))
            ->build();
        nusoap_base::setGlobalDebugLevel(9);
        $dbClient = new DbClient(DB_SERVICE_URL);
        //$log->debug("Queryng " . DB_SERVICE_URL, ["SelectStmnt" => $statement]);
        if (is_null($statement)) {
            http_response_code(500);
            die("situacion imposible");
        }
        $ret = $dbClient->select($statement);
        $log->debug("Query response: " . json_encode($ret, JSON_PRETTY_PRINT));
        $log->debug("dbClient debug info: " . $dbClient->getDebug());
        return ((is_array($ret) && array_key_exists("code", $ret) && $ret["code"] == 0) ? ["code" => 0, "msg" => "successful loged in"] : ["code" => -1, "msg" => "Invalid login data"]);
    }

    class LoginServer extends BaseServer {
        private $loginServiceUrl;
        function __construct($loginServiceUrl) {
            parent::__construct($loginServiceUrl);
            $this->loginServiceUrl = $loginServiceUrl;
            $this->register('checkLogin',
                      ['email' => 'xsd:string',
                            'passwd' => 'xsd:string'],
                      ['return' => "tns:stdMessage"],
                      $this->loginServiceUrl
            );
        }
    }
