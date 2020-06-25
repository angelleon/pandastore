<?php
    namespace PandaStore\Types;
    require_once __DIR__."/../../vendor/autoload.php";
    require_once __DIR__.'/SqlBuilder.php';

    use PHPUnit\Framework\TestCase;

    use PandaStore\Types\Sql\SqlBuilder;

    class StmntBuilderTest extends TestCase {

        /**
         * @test
         */
        public function testSelectStmnt() {
            $stmnt = [
                "columns"=> [
                    "column"=> "ID_USER"
                ],
                "tables"=> [
                    "table"=> "KART"
                ],
                "values"=> [
                    "value"=> "1"
                ],
                "database"=> "STORE_DB"
            ];
            $query = "";
            $values = [];
            SqlBuilder::insert($stmnt, $query, $values);
            $this->assertEquals("INSERT INTO KART(ID_USER) VALUES ( ? )", $query);
            $this->assertEquals([1], $values);
        }

        /**
         * @test
         */
        public function testUpdateStmnt() {

            $stmnt = [
                "columns"=> [
                    "column"=> "PRODUCT_COUNT"
                ],
                "tables"=> [
                    "table"=> "KART_PRODUCT"
                ],
                "values" => [
                    "value"=> "2"
                ],
                "conditionColumns"=> [
                    "column"=> [
                        "ID_PRODUCT",
                        "ID_USER"
                    ]
                ],
                "conditionOperators"=> [
                    "operator"=> ["=", "="]
                ],
                "conditionValues" => [
                    "value" => [1,1]
                ],
                "conditionBoolOperators" => [
                    "boolOperator" => "AND"
                ],
                "database"=> "STORE_DB"
            ];
            
            $query = "";
            $values = [];
            $result = SqlBuilder::update($stmnt, $query, $values);
            $this->assertNull($result);
            $this->assertEquals("UPDATE KART_PRODUCT SET PRODUCT_COUNT = ? WHERE ID_PRODUCT = ? AND ID_USER = ?", $query);
            $this->assertEquals([2, 1, 1], $values);
        }

        /**
         * @test
         */
        public function deleteStmntTest() {
            $stmnt = [
                "conditionColumns"=> [
                    "column"=> ["ID_USER", "ID_PRODUCT"]
                ],
                "tables"=> [
                    "table"=> "KART_PRODUCT"
                ],
                "conditionOperators" => [
                    "operator" => ["=", "="]
                ],
                "conditionValues"=> [
                    "value"=> ["1", "2"]
                ],
                "conditionBoolOperators" => [
                    "boolOperator" => "AND"
                ],
                "database"=> "STORE_DB"
            ];
            $query = "";
            $values = [];
            SqlBuilder::delete($stmnt, $query, $values);
            $this->assertEquals("DELETE FROM KART_PRODUCT WHERE ID_USER = ? AND ID_PRODUCT = ?", $query);
            $this->assertEquals([1, 2], $values);
        }
    }