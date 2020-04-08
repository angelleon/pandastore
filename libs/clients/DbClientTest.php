<?php
    namespace PandaStore\Clients;
    require_once __DIR__."/../../vendor/autoload.php";
    require_once __DIR__."/../types/DbStatement.php";
    require_once __DIR__."/../util/Encrypter.php";
    require_once __DIR__."/DbClient.php";

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use Monolog\Formatter\LineFormatter;
    use PHPUnit\Framework\TestCase;
    use nusoap_base;

    $formatter = new LineFormatter(null, null, true, true);
    $handler = new StreamHandler(__DIR__."/../../logs/".basename(__FILE__).".log", Logger::DEBUG);
    $handler->setFormatter($formatter);

    $log = new Logger(basename(__FILE__));
    $log->pushHandler($handler);

    use PandaStore\Types\DbStatement;
    use PandaStore\Util\Encrypter;


    class DbClientTest extends TestCase {
        
        function testInsert() {
            global $log;
            global $SERVICE_URL;
            $log->debug($SERVICE_URL);
            nusoap_base::setGlobalDebugLevel(9);
            $client = new DBClient($SERVICE_URL);
            $statement = (new DbStatement())
                ->column('username')
                ->column('passwd')
                ->into("User")
                ->value('testcase')
                ->value(Encrypter::encrypt('0'))
                ->build();
            $ret = $client->insert($statement);
            $log->debug($client->getDebug());
            $log->debug($client->request);
            $log->debug("gotten response from server");
            $log->debug(json_encode($ret, JSON_PRETTY_PRINT));
            $log->debug("ending test");
        } 

        /*function testSelect() {
            global $log;
            global $SERVICE_URL;
            $log->debug($SERVICE_URL);
            nusoap_base::setGlobalDebugLevel(9);
            $client = new DBClient($SERVICE_URL);
            $statement = (new DbStatement())
                ->column('username')
                ->column('passwd')
                ->from("User")
                //->where("userId")->eq(1)
                ->where("email")->eq("luianglenlop@gmail.com")
                ->limit(2)
                //->offset(1)
                ->build();
            $ret = $client->select($statement);
            $log->debug($client->getDebug());
            $log->debug($client->request);
            $log->debug(json_encode($statement, JSON_PRETTY_PRINT));
            $log->debug("gotten response from server");
            $log->debug(json_encode($ret, JSON_PRETTY_PRINT));
            $log->debug("ending test");
        }*/
    }