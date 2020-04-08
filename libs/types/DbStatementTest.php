<?php
    require __DIR__."/../../vendor/autoload.php";
    require __DIR__."/DbStatement.php";

    use PHPUnit\Framework\TestCase;
    use PandaStore\Types\DbStatement;

    class DbStatementTest extends TestCase {
        public function testSelectArray() {
            $statement = (new DbStatement())
                            ->column('userId')
                            ->column('surename')
                            ->from('User')
                            ->where('userId')->eq(0)
                            ->build();
            $expected_value = ["columns" => ['userId', 'surename'],
                                   "tables" => ['User'],
                                   "conditionColumns" => ['userId'],
                                   "conditionOperators" => ['='],
                                   "conditionValues" => [0],
                                   "conditionBoolOperators" => []];
            $this->assertEquals($expected_value, $statement);
        }

        public function testInsertArray() {
            $statement = (new DbStatement())
                ->column('username')
                ->column('passwd')
                ->into("User")
                ->value('testcase')
                ->value(0)
                ->build();
            $expected_value = ["columns" => ['username', 'passwd'],
                                   "tables" => ['User'],
                                   "conditionColumns" => [],
                                   "conditionOperators" => [],
                                   "conditionValues" => ['testcase', 0],
                                   "conditionBoolOperators" => []];
            //print_r($statement);
            //print_r($expected_value);
            $this->assertEquals($expected_value, $statement);
        }
    }