<?php

//namespace PandaStore\Servers;

require_once __DIR__ . "/../vendor/autoload.php";

require_once __DIR__ . '/BaseServer.php';

require_once __DIR__ . "/../clients/DbClient.php";
require_once __DIR__ . "/../types/dto/StmntBuilder.php";

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

use PandaStore\Clients\DbClient;
use PandaStore\Types\Dto\StmntBuilder;
use PandaStore\Servers\BaseServer;

// log init
$formatter = new LineFormatter("[%channel%][%level_name%] : %message% %context% \n", null, true, true); // allows witting LF in log entries
$handler = new StreamHandler(__DIR__ . "/../../logs/" . basename(__FILE__) . ".log", Logger::DEBUG);
$handler->setFormatter($formatter);
$log = new Logger(basename(__FILE__));
$log->pushHandler($handler);


function chkDbError(&$result, $parameter, $tag, &$log)
{
    if (!is_array($result)) {
        $msg = "Unexpected response from database service: " . json_encode($result, JSON_PRETTY_PRINT);
        $log->error($msg, ["line" => __LINE__]);
        return ["code" => -1, "msg" => $msg];
    }
    if ($result["code"] != 0) {
        return $result;
    }
    $result = $result["msg"];
    if (is_array($result) && count($result) == 0) {
        $msg = "$tag with value [$parameter] does not exists. Returning";
        $log->warning($msg, ["line" => __LINE__]);
        return ["code" => -1, "msg" => "$tag with value [$parameter] does not exists"];
    }
    return 0;
}

function productExists($productId, &$dbClient, &$error, &$log)
{
    StmntBuilder::setDatabase("STORE_DB");

    $stmnt = StmntBuilder::select("1 AS PRODUCT_EXISTS")
        ->from("PRODUCT")
        ->where("ID_PRODUCT")->eq($productId)
        ->build();
    $productExists = $dbClient->select($stmnt);

    $log->debug("DbClient response: " . json_encode($productExists, JSON_PRETTY_PRINT), ["line" => __LINE__]);

    $error = chkDbError($productExists, $productId, "ID_PRODUCT", $log);

    if ($error !== 0) {
        $log->debug("Error queryng database. Returning.");
        return false;
    }

    $productExists = intval($productExists[0]->PRODUCT_EXISTS) == 1;
    return $productExists;
}

function userExists($userId, &$dbClient, &$error, &$log)
{
    StmntBuilder::setDatabase("STORE_DB");
    $stmnt = StmntBuilder::select("1 AS USER_EXISTS")
        ->from("USER")
        ->where("ID_USER")->eq($userId)
        ->build();

    $userExists = $dbClient->select($stmnt);

    $log->debug("DbClient response: " . json_encode($userExists, JSON_PRETTY_PRINT), ["line" => __LINE__]);

    $error = chkDbError($userExists, $userId, "ID_USER", $log);

    if ($error !== 0) {
        $log->debug("Error queryng database. Returning.", ["line" => __LINE__]);
        return false;
    }

    $userExists = intval($userExists[0]->USER_EXISTS) == 1;
    return $userExists;
}

function kartExists($userId, &$dbClient, &$error, &$log)
{
    StmntBuilder::setDatabase("STORE_DB");
    $stmnt = StmntBuilder::select("COUNT(*) AS KART_EXISTS")
        ->from("KART")
        ->where("ID_USER")->eq($userId)
        ->build();

    $kartExists = $dbClient->select($stmnt);

    $log->debug("DbClient response: " . json_encode($kartExists, JSON_PRETTY_PRINT), ["line" => __LINE__]);

    $error = chkDbError($kartExists, $userId, "ID_USER", $log);

    if ($error !== 0) {
        $log->debug("Error queryng database. Returning.", ["line" => __LINE__]);
        return false;
    }

    $kartExists = intval($kartExists[0]->KART_EXISTS) >= 1;
    return $kartExists;
}

function productExistsInKart($userId, $productId, &$dbClient, &$error, &$log)
{
    StmntBuilder::setDatabase("STORE_DB");
    $stmnt = StmntBuilder::select("IFNULL(PRODUCT_COUNT, 0) AS PRODUCT_COUNT")
        ->from("KART_PRODUCT")
        ->where("ID_PRODUCT")->eq($productId)
        ->and("ID_USER")->eq($userId)
        ->build();

    $actualProductCount = $dbClient->select($stmnt);

    $log->debug("DbClient response: " . json_encode($actualProductCount, JSON_PRETTY_PRINT), ["line" => __LINE__]);
    $error = chkDbError($actualProductCount, $userId, "ID_USER", $log);

    if ($error !== 0) {
        return false;
    }
    $actualProductCount = intval($actualProductCount[0]->PRODUCT_COUNT);
    return $actualProductCount;
}


function kartPush($pushArgs)
{
    $formatter = new LineFormatter("[%channel%][%level_name%] : %message% %context% \n", null, true, true); // allows witting LF in log entries
    $handler = new StreamHandler(__DIR__ . "/../../logs/" . basename(__FILE__) . ".log", Logger::DEBUG);
    $handler->setFormatter($formatter);
    $log = new Logger(basename(__FILE__));
    $log->pushHandler($handler);

    if (is_null($pushArgs)) {
        $msg = "Recived null arguments";
        return ["code" => -1, "msg" => $msg];
    }
    $log->debug("Starting kartPush");
    $log->debug("Using arguments: " . json_encode($pushArgs, JSON_PRETTY_PRINT));

    $userId = $pushArgs["userId"];
    $productId = $pushArgs["productId"];
    $count = $pushArgs["count"];
    nusoap_base::setGlobalDebugLevel(9);
    $dbClient = new DbClient(DB_SERVICE_URL);

    $error = null;

    $userExists = userExists($userId, $dbClient, $error, $log);

    if (!$userExists) {
        return ["code" => -1, "msg" => "User [$userId] does not exist"];
    }

    $productExists = productExists($productId, $dbClient, $error, $log);

    if (!$productExists) {
        return ["code" => -1, "masg" => "Product [] does not exist"];
    }

    $kartExists = kartExists($userId, $dbClient, $error, $log);

    if (!$kartExists) {
        $log->debug("Kart not exists. Creating it.", ["line" => __LINE__]);
        StmntBuilder::setDatabase("STORE_DB");
        $stmnt = StmntBuilder::insert("ID_USER")
            ->into("KART")
            ->values($userId)
            ->build();
        $kartCreated = $dbClient->insert($stmnt);

        $log->debug("DbClient response: " . json_encode($kartExists, JSON_PRETTY_PRINT), ["line" => __LINE__]);

        $dbError = chkDbError($kartCreated, $userId, "ID_USER", $log);

        if ($dbError !== 0) {
            $msg = "Unable to create kart for user [$userId]";
            $log->debug($msg, ["line" => __LINE__]);
            $dbError["msg"] = $msg;
            return $dbError;
        }
    }

    $productExistsInKart = productExistsInKart($userId, $productId, $dbClient, $error, $log);

    if (!$productExistsInKart) {
        StmntBuilder::setDatabase("STORE_DB");
        $stmnt = StmntBuilder::insert("ID_USER")
            ->column("ID_PRODUCT")
            ->column("PRODUCT_COUNT")
            ->into("KART_PRODUCT")
            ->values($userId)
            ->value($productId)
            ->value($count)
            ->build();
        $kartCreated = $dbClient->insert($stmnt);
        $log->debug("DbClient response: " . json_encode($kartCreated, JSON_PRETTY_PRINT), ["line" => __LINE__]);

        //return ["code" => -3.14, "msg" => "expected breakpoint"];
        $dbError = chkDbError($kartCreated, $userId, "ID_USER", $log);

        if ($dbError !== 0) {
            return $dbError;
        }

        $msg = "[$count] product" . ($count > 0 ? "s" : "") . " [$productId] added to kart of user [$userId]";
        $log->debug($msg, ["line" => __LINE__]);
        return ["code" => 0, "msg" => $msg];
    }

    $totalProduct = $productExistsInKart + $count;
    $log->debug("Updating product count to [$totalProduct]");

    StmntBuilder::setDatabase("STORE_DB");
    $stmnt = StmntBuilder::update("KART_PRODUCT")
        ->set("PRODUCT_COUNT")->to($totalProduct)
        ->where("ID_PRODUCT")->eq($productId)
        ->and("ID_USER")->eq($userId)
        ->build();
    $kartUpdated = $dbClient->update($stmnt);

    $log->debug("DbClient response: " . json_encode($kartUpdated, JSON_PRETTY_PRINT), ["line" => __LINE__]);

    //return ["code" => -3.14, "msg" => "expected breakpoint"];
    $dbError = chkDbError($kartUpdated, $userId, "ID_USER", $log);

    if ($dbError !== 0) {
        return $dbError;
    }

    $msg = "[$count] product" . ($count > 0 ? "s" : "") . " [$productId] added to kart of user [$userId]";
    $log->debug($msg, ["line" => __LINE__]);
    return ["code" => 0, "msg" => $msg];
}


function kartPop($popArgs)
{
    $formatter = new LineFormatter("[%channel%][%level_name%] : %message% %context% \n", null, true, true); // allows witting LF in log entries
    $handler = new StreamHandler(__DIR__ . "/../../logs/" . basename(__FILE__) . ".log", Logger::DEBUG);
    $handler->setFormatter($formatter);
    $log = new Logger(basename(__FILE__));
    $log->pushHandler($handler);

    if (is_null($popArgs)) {
        $msg = "Recived null arguments";
        return ["code" => -1, "msg" => $msg];
    }
    $log->debug("Starting kartPop");
    $log->debug("Using arguments: " . json_encode($popArgs, JSON_PRETTY_PRINT));

    $userId = $popArgs["userId"];
    $productId = $popArgs["productId"];
    $count = $popArgs["count"];


    nusoap_base::setGlobalDebugLevel(9);
    $dbClient = new DbClient(DB_SERVICE_URL);

    $error = null;

    $userExists = userExists($userId, $dbClient, $error, $log);

    if (!$userExists) {
        return ["code" => -1, "msg" => "User [$userId] does not exist"];
    }

    $productExists = productExists($productId, $dbClient, $error, $log);

    if (!$productExists) {
        return ["code" => -1, "masg" => "Product [] does not exist"];
    }

    $kartExists = kartExists($userId, $dbClient, $error, $log);

    if (!$kartExists) {
        $msg = "Kart not exists for user [$userId]. Skipping request.";
        $log->debug($msg, ["line" => __LINE__]);
        return ["code" => 0, "msg" => $msg];
    }

    $productExistsInKart = productExistsInKart($userId, $productId, $dbClient, $error, $log);

    if (!$productExistsInKart) {
        $msg = "Product [$productId] does not exist in kart of user [$userId], ignoring request";
        $log->debug($msg, ["line" => __LINE__]);
        return ["code" => 0, "msg" => $msg];
    }

    $productCount = $productExistsInKart;

    if ($count >= $productCount) {
        StmntBuilder::setDatabase("STORE_DB");
        $stmnt = StmntBuilder::delete()
            ->from("KART_PRODUCT")
            ->where("ID_USER")->eq($userId)
            ->and("ID_PRODUCT")->eq($productId)
            ->build();
        $productDeleted = $dbClient->delete($stmnt);
        $dbError = chkDbError($productDeleted, $userId, "ID_USER", $log);

        if ($dbError !== 0) {
            return $dbError;
        }
        $msg = "Succesfull deleted [$count] prducts for user [$userId] kart";
        $log->debug($msg);
        return ["code" => 0, "msg" => $msg];
    }

    StmntBuilder::setDatabase("STORE_DB");
    $stmnt = StmntBuilder::update("KART_PRODUCT")
        ->set("PRODUCT_COUNT")->to($productCount - $count)
        ->where("ID_USER")->eq($userId)
        ->and("ID_PRODUCT")->eq($productId)
        ->build();
    $kartUpdated = $dbClient->update($stmnt);

    $log->debug("DbClient response: " . json_encode($kartUpdated, JSON_PRETTY_PRINT), ["line" => __LINE__]);

    //return ["code" => -3.14, "msg" => "expected breakpoint"];
    $dbError = chkDbError($kartUpdated, $userId, "ID_USER", $log);

    if ($dbError !== 0) {
        return $dbError;
    }

    return ["code" => 0, "msg" => "Succesfull deleted [$count] prducts for user [$userId] kart"];
}

function kartClear($clearArgs)
{
    $formatter = new LineFormatter("[%channel%][%level_name%] : %message% %context% \n", null, true, true); // allows witting LF in log entries
    $handler = new StreamHandler(__DIR__ . "/../../logs/" . basename(__FILE__) . ".log", Logger::DEBUG);
    $handler->setFormatter($formatter);
    $log = new Logger(basename(__FILE__));
    $log->pushHandler($handler);

    if (is_null($clearArgs)) {
        $msg = "Recived null arguments";
        return ["code" => -1, "msg" => $msg];
    }
    $log->debug("Starting kartPop");
    $log->debug("Using arguments: " . json_encode($clearArgs, JSON_PRETTY_PRINT));

    $userId = $clearArgs["userId"];
    $productId = $clearArgs["productId"];

    nusoap_base::setGlobalDebugLevel(9);
    $dbClient = new DbClient(DB_SERVICE_URL);

    $error = null;

    $userExists = userExists($userId, $dbClient, $error, $log);

    if (!$userExists) {
        return ["code" => -1, "msg" => "User [$userId] does not exist"];
    }

    $productExists = productExists($productId, $dbClient, $error, $log);

    if (!$productExists) {
        return ["code" => -1, "masg" => "Product [] does not exist"];
    }

    $kartExists = kartExists($userId, $dbClient, $error, $log);

    if (!$kartExists) {
        $msg = "Kart not exists for user [$userId]. Skipping request.";
        $log->debug($msg, ["line" => __LINE__]);
        return ["code" => 0, "msg" => $msg];
    }

    $productExistsInKart = productExistsInKart($userId, $productId, $dbClient, $error, $log);

    if (!$productExistsInKart) {
        $msg = "Product [$productId] does not exist in kart of user [$userId], ignoring request";
        $log->debug($msg, ["line" => __LINE__]);
        return ["code" => 0, "msg" => $msg];
    }

    StmntBuilder::setDatabase("STORE_DB");
    $stmnt = StmntBuilder::delete()
        ->from("KART_PRODUCT")
        ->where("ID_USER")->eq($userId)
        ->and("ID_PRODUCT")->eq($productId)
        ->build();
    $productDeleted = $dbClient->delete($stmnt);
    $dbError = chkDbError($productDeleted, $userId, "ID_USER", $log);

    if ($dbError !== 0) {
        return $dbError;
    }
    $msg = "Succesfull deleted all prducts for user [$userId] kart";
    $log->debug($msg);
    return ["code" => 0, "msg" => $msg];
}

function kartGet($getArgs)
{
    $formatter = new LineFormatter("[%channel%][%level_name%] : %message% %context% \n", null, true, true); // allows witting LF in log entries
    $handler = new StreamHandler(__DIR__ . "/../../logs/" . basename(__FILE__) . ".log", Logger::DEBUG);
    $handler->setFormatter($formatter);
    $log = new Logger(basename(__FILE__));
    $log->pushHandler($handler);

    if (is_null($getArgs)) {
        $msg = "Recived null arguments";
        return ["code" => -1, "msg" => $msg];
    }
    $log->debug("Starting kartPop");
    $log->debug("Using arguments: " . json_encode($getArgs, JSON_PRETTY_PRINT));

    $userId = $getArgs["userId"];

    nusoap_base::setGlobalDebugLevel(9);
    $dbClient = new DbClient(DB_SERVICE_URL);

    $error = null;

    $userExists = userExists($userId, $dbClient, $error, $log);

    if (!$userExists) {
        return ["code" => -1, "msg" => "User [$userId] does not exist   srfdsx"];
    }

    $kartExists = kartExists($userId, $dbClient, $error, $log);

    if (!$kartExists) {
        $msg = "Kart not exists for user [$userId]. Skipping request.";
        $log->debug($msg, ["line" => __LINE__]);
        return ["code" => 0, "msg" => $msg];
    }

    $stmnt = StmntBuilder::select("P.ID_PRODUCT AS ID_PRODUCT")
        ->column("P.PRODUCT_NAME AS PRODUCT_NAME")
        ->column("P.PRODUCT_DESC AS PRODUCT_DESC")
        ->column("P.COST AS COST")
        ->column("KP.PRODUCT_COUNT AS PRODUCT_COUNT")
        ->from("PRODUCT P")
        ->from("KART_PRODUCT KP")
        ->where("KP.ID_PRODUCT = P.ID_PRODUCT AND KP.ID_USER ")->eq($userId)
        ->build();

    $products = $dbClient->select($stmnt);

    $dbError = chkDbError($products, $userId, "ID_USER", $log);

    if ($dbError !== 0) {
        $msg = "Can not get products for user [$userId]";
        $log->debug($msg);
        $dbError["msg"] = $msg;
        return $dbError;
    } 

    $msg = json_encode($products);
    $log->debug("Gotten products for user [$userId] as: " .json_encode($products, JSON_PRETTY_PRINT) );
    return ["code" => 0, "msg" => $msg];
}


class KartServer extends BaseServer
{
    private $kartServiceUrl;

    public function __construct($kartServiceUrl)
    {
        $this->kartServiceUrl = $kartServiceUrl;

        $this->configureWSDL("WSDLTST", $this->kartServiceUrl);
        $this->wsdl->schemaTargetNamespace = $this->kartServiceUrl;
        $this->wsdl->addComplexType(
            "stdMessage",
            "complexType",
            "struct",
            "all",
            "",
            [
                "code" => ["name" => "code", "type" => "xsd:int"],
                "msg"  => ["name" => "msg", "type" => "xsd:string"]
            ]
        );
        /*
        $this->wsdl->addComplexType(
            "tUserId",
            "complexType",
            "struct",
            "all",
            "",
            [
                "userId" => ["name" => "userId", "type" => "xsd:int"]
            ]
        );

        $this->wsdl->addComplexType(
            "tProductId",
            "complexType",
            "struct",
            "all",
            "",
            [
                "productId" => ["name" => "productId", "type" => "xsd:int"]
            ]
        );

        $this->wsdl->addComplexType(
            "tKartCount",
            "complexType",
            "struct",
            "all",
            "",
            [
                "count" => ["name" => "count", "type" => "xsd:int"]
            ]
        );*/

        $this->wsdl->addComplexType(
            "tKartArgs",
            "complexType",
            "struct",
            "all",
            "",
            [
                "userId" => ["name" => "userId", "type" => "xsd:int"],
                "productId" => ["name" => "productId", "type" => "xsd:int"],
                "count" => ["name" => "count", "type" => "xsd:int"]
            ]
        );

        $this->register(
            "kartPush",
            ["pushArgs" => "tns:tKartArgs"],
            ["return" => "tns:stdMessage"],
            $this->kartServiceUrl
        );
        $this->register(
            "kartPop",
            ["popArgs" => "tns:tKartArgs"],
            ["return" => "tns:stdMessage"],
            $this->kartServiceUrl
        );
        $this->register(
            "kartClear",
            ["clearArgs" => "tns:tKartArgs"],
            ["return" => "tns:stdMessage"],
            $this->kartServiceUrl
        );
        $this->register(
            "kartGet",
            ["getArgs" => "tns:tKartArgs"],
            ["return" => "tns:stdMessage"],
            $this->kartServiceUrl
        );
    }
}
