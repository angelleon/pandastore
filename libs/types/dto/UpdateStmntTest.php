<?php
    namespace PandaStore\Types\Dto;
    require_once __DIR__."/../../vendor/autoload.php";
    require_once __DIR__."/UpdateStmnt.php";

    use PHPUnit\Framework\TestCase;

    class SelectStmntTest extends TestCase {
        /**
         * @test
         */
        function updateStmntTest() {
            $userId = 1;
            $stmnt = (new UpdateStmnt("STORE_DB"))->table("KART_PRODUCT")
                ->set("PRODUCT_COUNT")->to(1)
                ->where("ID_PRODUCT")->eq(2)
                ->and("ID_USER")->eq(3)
                ->build();
            $expected = ["updateStmnt" => [
                "columns" => ["column" => ["PRODUCT_COUNT"]],
                "tables" => ["table" => ["KART_PRODUCT"]],
                "values" => ["value" => [1]],
                "conditionColumns" => ["column" => ["ID_PRODUCT", "ID_USER"]],
                "conditionOperators" => ["operator" => ["=", "="]],
                "conditionBoolOperators" => ["boolOperator" => ["AND"]],
                "conditionValues" => ["value" => [2, 3]],
                "database" => "STORE_DB"
                ]];
            $this->assertNotNull($stmnt);
            $this->assertIsArray($stmnt);
            $this->assertEquals($expected, $stmnt);
        }
    }