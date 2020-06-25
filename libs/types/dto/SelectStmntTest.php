<?php
    namespace PandaStore\Types\Dto;
    require_once __DIR__."/../../vendor/autoload.php";
    require_once __DIR__."/SelectStmnt.php";

    use PHPUnit\Framework\TestCase;

    class SelectStmntTest extends TestCase {
        /**
         * @test
         */
        function selectStmntTest() {
            $stmnt = (new SelectStmnt())
                ->column("column1")
                ->column("column2")
                ->from("table1")
                ->from("table2")
                ->where("column2")->eq("value")
                ->build();
            $expected = ["selectStmnt" => [
                "distinct" => false,
                "columns" => ["column" => ["column1", "column2"]],
                "tables" => ["table" => ["table1", "table2"]],
                "conditionColumns" => ["column" => ["column2"]],
                "conditionOperators" => ["operator" => ["="]],
                "conditionValues" => ["value" => ["value"]],
                "conditionBoolOperators" => ["boolOperator" => []],
                "limitCount" => null,
                "limitOffset" => null
                ]];
            $this->assertNotNull($stmnt);
            $this->assertIsArray($stmnt);
            $this->assertEquals($expected, $stmnt);
            $stmnt = (new SelectStmnt())
                ->column("*")
                ->from("station")
                ->build();
            $expected = ["selectStmnt" => [
                "distinct" => false,
                "columns" => ["column" => ["*"]],
                "tables" => ["table" => ["station"]],
                "conditionColumns" => ["column" => []],
                "conditionOperators" => ["operator" => []],
                "conditionValues" => ["value" => []],
                "conditionBoolOperators" => ["boolOperator" => []],
                "limitCount" => null,
                "limitOffset" => null
            ]];
            $this->assertNotNull($stmnt);
            $this->assertIsArray($stmnt);
            $this->assertEquals($expected, $stmnt);
            $stmnt = (new SelectStmnt())
                ->distinct()
                ->column("firstName")
                ->column("nickname")
                ->from("Friends")
                ->where("nickname")->like("%brain%")
                ->build();
            $expected = ["selectStmnt" => [
                "distinct" => true,
                "columns" => ["column" => ["firstName", "nickname"]],
                "tables" => ["table" => ["Friends"]],
                "conditionColumns" => ["column" => ["nickname"]],
                "conditionOperators" => ["operator" => ["LIKE"]],
                "conditionValues" => ["value" => ["%brain%"]],
                "conditionBoolOperators" => ["boolOperator" => []],
                "limitCount" => null,
                "limitOffset" => null
            ]];
            $this->assertNotNull($stmnt);
            $this->assertIsArray($stmnt);
            $this->assertEquals($expected, $stmnt);
        }
    }