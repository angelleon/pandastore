<?php
    namespace PandaStore\Types\Dto;
    require_once __DIR__."/DbStmnt.php";

    use Exception;

    class SelectStmnt extends DbStmnt {
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

        private $distinctModifier;
        private $columns;
        private $tables;
        private $conditionColumns;
        private $conditionOperators;
        private $conditionBoolOperators;
        private $conditionValues;
        private $limitCount;
        private $limitOffset;

        function __construct($database=null) {
            $this->state = SelectStmnt::COLUMN_LIST;
            $this->distinctModifier = false;
            $this->columns = [];
            $this->tables = [];
            $this->conditionColumns = [];
            $this->conditionOperators = [];
            $this->conditionBoolOperators = [];
            $this->conditionValues = [];
            $this->limitCount = null;
            $this->limitOffset = null;
            $this->database = is_null($database) ? "" : $database;
        }

        

        public function build() {
            /*
            $arrayStmnt = ["selectStmnt" => [
                        "distinct" => $this->distinctModifier,
                        "columns" => ["column" => $this->columns],
                        "tables" => ["table" => $this->tables],
                        "conditionColumns" => ["column" => $this->conditionColumns],
                        "conditionOperators" => ["operator" => $this->conditionOperators],
                        "conditionValues" => ["value" => $this->conditionValues],
                        "conditionBoolOperators" => ["boolOperator" => $this->conditionBoolOperators],
                ]
            ];
            if (!is_null($this->limitCount)) {
                $arrayStmnt["selectStmnt"]["limitCount"] = $this->limitCount;
                if (!is_null($this->limitOffset)) {
                    $arrayStmnt["selectStmnt"]["limitOffset"] = $this->limitOffset;
                }
            }
            return $arrayStmnt;
            */
            return ["selectStmnt" => [
                "distinct" => $this->distinctModifier,
                "columns" => ["column" => $this->columns],
                "tables" => ["table" => $this->tables],
                "conditionColumns" => ["column" => $this->conditionColumns],
                "conditionOperators" => ["operator" => $this->conditionOperators],
                "conditionValues" => ["value" => $this->conditionValues],
                "conditionBoolOperators" => ["boolOperator" => $this->conditionBoolOperators],
                "limitCount" => $this->limitCount,
                "limitOffset" => $this->limitOffset,
                "database" => $this->database
                ]
            ];
        }

        public function from($table) {
            $this->checkState(SelectStmnt::FROM_CLAUSE);
            $this->tables[] = $table;
            return $this;
        }

        

        public function distinct() {
            $this->distinctModifier = true;
            return $this;
        }

        public function column($column) {
            $this->checkState(SelectStmnt::COLUMN_LIST | SelectStmnt::CONDITION_CLAUSE);
            if ($this->state & SelectStmnt::COLUMN_LIST) {
                $this->columns[] = $column;
            } else if ($this->state & SelectStmnt::CONDITION_CLAUSE) {
                $this->conditionColumns[] = $column;
            }
            return $this;
        }

        private function addConditionOperator($operator) {
            $this->checkState(SelectStmnt::CONDITION_CLAUSE);
            $operators = ['=', '!=', '>', '<', '>=', '<=', 'like'];
            if (!in_array($operator, $operators, true)){
                throw new Exception('Invalid operator');
            }
            $this->conditionOperators[] = $operator;
            return $this;
        }

        private function addConditonBoolOperator($operator) {
            $this->checkState(SelectStmnt::CONDITION_CLAUSE);
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

        public function like($pattern) {
            $this->checkState(SelectStmnt::CONDITION_CLAUSE);
            $this->conditionOperators[] = "LIKE";
            $this->conditionValues[] = $pattern;
            return $this;
        }

        public function value($value) {
            $this->checkState(SelectStmnt::COLUMN_LIST | SelectStmnt::CONDITION_CLAUSE);
            if ($this->state & SelectStmnt::COLUMN_LIST) {
                $this->columns[] = $value;
            } else if ($this->state & SelectStmnt::CONDITION_CLAUSE) {
                $this->conditionValues[] = $value;
            }
            return $this;
        }

        public function where($column) {
            $this->checkState(SelectStmnt::CONDITION_CLAUSE);
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