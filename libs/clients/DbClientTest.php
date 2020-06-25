<?php
    namespace PandaStore\Clients;
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../types/dto/StmntBuilder.php";
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

    use PandaStore\Types\Dto\StmntBuilder;
    use PandaStore\Util\Encrypter;


    class DbClientTest extends TestCase {
        /*
        function testInsert() {
            global $log;
            nusoap_base::setGlobalDebugLevel(9);
            $client = new DBClient(DB_SERVICE_URL);
            $statement = (new InsertStmnt())
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
            $this->assertIsArray($ret);
        } */

        /**
         * @test
         */
        function testSelect() {
            global $log;
            $log->debug(DB_SERVICE_URL);
            nusoap_base::setGlobalDebugLevel(9);
            $client = new DBClient(DB_SERVICE_URL);
            $statement = StmntBuilder::select('userId')
                ->column('passwd')
                ->from("User")
                //->where("userId")->eq(1)
                ->where("email")->eq("luianglenlop@gmail.com")
                ->and("passwd")->eq(Encrypter::encrypt("pandastore"))
                ->limit(1)
                //->offset(1)
                ->build();
            $this->assertNotNull($statement);
            $ret = $client->select($statement);
            $log->debug($client->getDebug());
            $log->debug($client->request);
            $log->debug(json_encode($statement, JSON_PRETTY_PRINT));
            $log->debug("gotten response from server");
            $log->debug(json_encode($ret, JSON_PRETTY_PRINT));
            $log->debug("ending test");
            $this->assertIsArray($ret);
            $this->assertArrayHasKey("code", $ret);
            $this->assertArrayHasKey("msg", $ret);
        }
    }