<?php
    //namespace PandaStore\Servers;
    require_once __DIR__.'/../vendor/autoload.php';
    require_once __DIR__.'/../Url.php';
    require_once __DIR__."/BaseServer.php";
    require_once __DIR__."/../types/StmntBuilder.php";
    require_once __DIR__."/../clients/DbClient.php";

    use PandaStore\Clients\DbClient;
    use PandaStore\Servers\BaseServer;
    use PandaStore\Types\StmntBuilder;

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use Monolog\Formatter\LineFormatter;

    

    $formatter = new LineFormatter(null, null, true, true);
    $handler = new StreamHandler(__DIR__."/../../logs/".basename(__FILE__).".log", Logger::DEBUG);
    $handler->setFormatter($formatter);

    $log = new Logger(basename(__FILE__));
    $log->pushHandler($handler);

    $log->debug("starting debug");



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
        return ["code" => 0, "msg" => "$result"];
    }

    function mkProduct($name, $price, $photo_hash, $description, $tags) {
        global $log;
        $statement = StmntBuilder::insert("name")
                ->column("price")
                ->column("photoHash")
                ->column("description")
                ->column("tags")
                ->into("Product")
                ->value($name)
                ->value($price)
                ->value($photo_hash)
                ->value($description)
                ->value($tags)
                ->build();
        $dbClient = new DbClient(DB_SERVICE_URL);
        $log->debug("executing insert statement");
        $result = $dbClient->insert($statement);
        $log->debug("insert result: ".json_encode($result, JSON_PRETTY_PRINT));
        return ["code" => 0, "msg" => "$result"];
    }

    function rmProduct($productId) {
        global $log;
        $statement = StmntBuilder::update("Product")
                ->set("active")->to("false")
                ->where("idProduct")->eq($productId)
                ->build();
        $dbClient = new DbClient(DB_SERVICE_URL);
        $log->debug("executing insert statement");
        $result = $dbClient->insert($statement);
        $log->debug("insert result: ".json_encode($result, JSON_PRETTY_PRINT));
        return ["code" => 0, "msg" => "$result"];
    }

    function viProduct($productId) {
        global $log;
        $statement = StmntBuilder::update("Product")
                ->set("active")->to("false")
                ->where("idProduct")->eq($productId)
                ->build();
        $dbClient = new DbClient(DB_SERVICE_URL);
        $log->debug("executing insert statement");
        $result = $dbClient->insert($statement);
        $log->debug("insert result: ".json_encode($result, JSON_PRETTY_PRINT));
        return ["code" => 0, "msg" => "$result"];
    }

    class ProductManagerServer extends BaseServer {
        private $productManagerServiceUrl;
        function __construct($productManagerServiceUrl) {
            parent::__construct($productManagerServiceUrl);
            $this->$productManagerServiceUrl = $productManagerServiceUrl;
            $this->register('mkProduct',
                ['name' => 'xsd:string',
                    'price' => 'xsd:double',
                    'photo_hash' => 'xsd:string',
                    'description' => 'xsd:string',
                    'tags' => "xsd:string"],
                ['return' => "tns:stdMessage"],
                $this->productManagerServiceUrl);
            $this->register('rmProduct',
                ['productId' => 'xsd:int'],
                ['return' => "tns:stdMessage"],
                $this->productManagerServiceUrl);
            $this->register('lsProduct',
                ['productCount' => 'xsd:int',
                    'pageNum' => 'xsd:int'],
                ['return' => "tns:stdMessage"],
                $this->productManagerServiceUrl);
            $this->register('viProduct',
                ['productCount' => 'xsd:int',
                    'pageNum' => 'xsd:int'],
                ['return' => "tns:stdMessage"],
                $this->productManagerServiceUrl);
        }
    }