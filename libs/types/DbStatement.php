<?php 
    namespace PandaStore\Types;
    use Exception;

    require __DIR__."/../interfaces/Builder.php";

    use PandaStore\Interfaces\Builder;
    
    class DbStatement implements Builder {
        /**
         * states
         * 0 columns or values to affect
         * 1 from clause
         * 2 conditions
         */
        const COLUMN_LIST = 0b01;
        const FROM_CLAUSE = 0b10;
        const INTO_CLAUSE = 0b10;
        const CONDITION_CLAUSE = 0b100;

        private $state;
        private $tables;
        private $columns;
        private $conditionColumns;
        private $conditionOperators;
        private $conditionBoolOperators;
        private $conditionValues;
        private $limitCount;
        private $limitOffset;

        function __construct() {
            $this->state = DbStatement::COLUMN_LIST;
            $this->tables = [];
            $this->columns = [];
            $this->conditionColumns = [];
            $this->conditionOperators = [];
            $this->conditionBoolOperators = [];
            $this->conditionValues = [];
            $this->limitCount = null;
            $this->limitOffset = null;
        }

        private function checkState($stateFlags) {
            if (!($stateFlags & $this->state
                  || ($stateFlags >> 1) & $this->state
                 )
               ) {
                $value = ($stateFlags >> 1) & $this->state;
                throw new Exception("Invalid statement\ncurrent state: $this->state, flags: $stateFlags, $value");
            } else if (($stateFlags >> 1) & $this->state) {
                $this->state <<= 1;
            }
        }

        public function build() {
            return ["statement" => [
                        "columns" => ["column" => $this->columns],
                        "tables" => ["table" => $this->tables],
                        "conditionColumns" => ["column" => $this->conditionColumns],
                        "conditionOperators" => ["operator" => $this->conditionOperators],
                        "conditionValues" => ["value" => $this->conditionValues],
                        "conditionBoolOperators" => ["boolOperator" => $this->conditionBoolOperators],
                        "limitCount" => $this->limitCount,
                        "limitOffset" => $this->limitOffset
                ]
            ];
        }

        public function from($table) {
            $this->checkState(DBStatement::FROM_CLAUSE);
            array_push($this->tables, $table);
            return $this;
        }

        public function into($table) {
            $this->checkState(DBStatement::INTO_CLAUSE);
            if (count($this->tables) > 0) {
                throw new Exception("Can not insert in more than one table");
            }
            array_push($this->tables, $table);
            return $this;
        }

        public function column($column) {
            $this->checkState(DbStatement::COLUMN_LIST | DBStatement::CONDITION_CLAUSE);
            if ($this->state & DbStatement::COLUMN_LIST) {
                array_push($this->columns, $column);
            } else if ($this->state & DbStatement::CONDITION_CLAUSE) {
                array_push($this->conditionColumns, $column);
            }
            return $this;
        }

        private function addConditionOperator($operator) {
            $this->checkState(DbStatement::CONDITION_CLAUSE);
            $operators = ['=', '!=', '>', '<', '>=', '<=', 'like'];
            if (!in_array($operator, $operators, true)){
                throw new Exception('Invalid operator');
            }
            array_push($this->conditionOperators, $operator);
            return $this;
        }

        private function addConditonBoolOperator($operator) {
            $this->checkState(DbStatement::CONDITION_CLAUSE);
            array_push($this->conditionBoolOperators, $operator);
        }

        public function eq($value) {
            $this->addConditionOperator('=');
            $this->value($value);
            return $this;
        }

        public function ne($value) {
            $this->addConditionOperator('!=');
            $this->value($value);
            return $this;
        }

        public function gt($value) {
            $this->addConditionOperator('>');
            $this->value($value);
            return $this;
        }

        public function lt($value) {
            $this->addConditionOperator('<');
            $this->value($value);
            return $this;
        }

        public function ge($value) {
            $this->addConditionOperator('>=');
            $this->value($value);
            return $this;
        }

        public function le($value) {
            $this->addConditionOperator('<=');
            $this->value($value);
            return $this;
        }

        public function value($value) {
            $this->checkState(DbStatement::COLUMN_LIST | DbStatement::CONDITION_CLAUSE);
            if ($this->state & DbStatement::COLUMN_LIST) {
                array_push($this->columns, $value);
            } else if ($this->state & DbStatement::CONDITION_CLAUSE) {
                array_push($this->conditionValues, $value);
            }
            return $this;
        }

        public function where($column) {
            $this->checkState(DbStatement::CONDITION_CLAUSE);
            $this->column($column);
            return $this;
        }

        public function and($column) {
            $this->addConditonBoolOperator('AND');
            $this->column($column);
            return $this;
        }

        public function or($column) {
            $this->addConditonBoolOperator('OR');
            $this->column($column);
            return $this;
        }

        public function not($column) {
            $this->addConditonBoolOperator('NOT');
            $this->column($column);
            return $this;
        }
        public function limit($count) {
            $this->limitCount = $count;
            return $this;
        }
        public function offset($offset) {
            $this->limitOffset = $offset;
            return $this;
        }
    }