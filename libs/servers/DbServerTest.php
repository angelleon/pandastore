<?php
    require_once __DIR__."/../../vendor/autoload.php";
    require_once __DIR__."/DbServer.php";
    require_once __DIR__."/../types/DbStatement.php";

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use Monolog\Formatter\LineFormatter;
    use nusoap_base;

    $formatter = new LineFormatter(null, null, true, true);
    $handler = new StreamHandler(__DIR__."/../../logs/".basename(__FILE__).".log", Logger::DEBUG);
    $handler->setFormatter($formatter);

    $log = new Logger(basename(__FILE__));
    $log->pushHandler($handler);

    use PHPUnit\Framework\TestCase;
    use PandaStore\Types\DbStatement;

    class DbServerTest extends TestCase {
        function testInsert() {
            //$this->assertEquals(1, DbInsert(null));
        }

        function testSelect() {
            global $log;
            $statement = (new DbStatement())
                ->column('email')
                ->column('passwd')
                ->from("User")
                //->where("userId")->eq(1)
                ->where("email")->eq("luianglenlop@gmail.com")
                ->limit(2)
                //->offset(1)
                ->build();
            $statement = $statement["statement"];
            $log->debug("statement: ".json_encode($statement, JSON_PRETTY_PRINT));
            $ret = DbSelect($statement);
            $log->debug("result: ".json_encode($ret, JSON_PRETTY_PRINT));
        }
    }