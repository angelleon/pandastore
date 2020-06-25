<?php

namespace PandaStore\Types\Dto;

require_once __DIR__ . "/DbStmnt.php";

class DeleteStmnt extends DbStmnt
{
    /**
     * states
     * 0 columns or values to affect
     * 1 from clause
     * 2 conditions
     */
    const FROM_CLAUSE = 0b01;
    const CONDITION_CLAUSE = 0b10;

    private $tables;
    private $conditionColumns;
    private $conditionOperators;
    private $conditionBoolOperators;
    private $conditionValues;

    function __construct($database = null)
    {
        $this->state = DeleteStmnt::FROM_CLAUSE;
        $this->tables = [];
        $this->conditionColumns = [];
        $this->conditionOperators = [];
        $this->conditionBoolOperators = [];
        $this->conditionValues = [];
        $this->database = is_null($database) ? "" : $database;
    }

    public function column($column)
    {
        $this->checkState(DeleteStmnt::CONDITION_CLAUSE);
        $this->conditionColumns[] = $column;
        return $this;
    }



    public function build()
    {
        /*
            $arrayStmnt = ["DeleteStmnt" => [
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
                $arrayStmnt["DeleteStmnt"]["limitCount"] = $this->limitCount;
                if (!is_null($this->limitOffset)) {
                    $arrayStmnt["DeleteStmnt"]["limitOffset"] = $this->limitOffset;
                }
            }
            return $arrayStmnt;
            */
        return ["deleteStmnt" => [
            "tables" => ["table" => $this->tables],
            "conditionColumns" => ["column" => $this->conditionColumns],
            "conditionOperators" => ["operator" => $this->conditionOperators],
            "conditionValues" => ["value" => $this->conditionValues],
            "conditionBoolOperators" => ["boolOperator" => $this->conditionBoolOperators],
            "database" => $this->database
        ]];
    }

    public function from($table)
    {
        $this->checkState(DeleteStmnt::FROM_CLAUSE);
        $this->tables[] = $table;
        return $this;
    }

    private function addConditionOperator($operator)
    {
        $this->checkState(DeleteStmnt::CONDITION_CLAUSE);
        $operators = ['=', '!=', '>', '<', '>=', '<=', 'like'];
        if (!in_array($operator, $operators, true)) {
            throw new \Exception('Invalid operator');
        }
        $this->conditionOperators[] = $operator;
        return $this;
    }

    private function addConditonBoolOperator($operator)
    {
        $this->checkState(DeleteStmnt::CONDITION_CLAUSE);
        array_push($this->conditionBoolOperators, $operator);
    }

    public function eq($value)
    {
        $this->addConditionOperator('=');
        $this->value($value);
        return $this;
    }

    public function ne($value)
    {
        $this->addConditionOperator('!=');
        $this->value($value);
        return $this;
    }

    public function gt($value)
    {
        $this->addConditionOperator('>');
        $this->value($value);
        return $this;
    }

    public function lt($value)
    {
        $this->addConditionOperator('<');
        $this->value($value);
        return $this;
    }

    public function ge($value)
    {
        $this->addConditionOperator('>=');
        $this->value($value);
        return $this;
    }

    public function le($value)
    {
        $this->addConditionOperator('<=');
        $this->value($value);
        return $this;
    }

    public function like($pattern)
    {
        $this->checkState(DeleteStmnt::CONDITION_CLAUSE);
        $this->conditionOperators[] = "LIKE";
        $this->conditionValues[] = $pattern;
        return $this;
    }

    public function value($value)
    {
        $this->checkState(DeleteStmnt::CONDITION_CLAUSE);
        $this->conditionValues[] = $value;
        return $this;
    }

    public function where($column)
    {
        $this->checkState(DeleteStmnt::CONDITION_CLAUSE);
        $this->column($column);
        return $this;
    }

    public function and($column)
    {
        $this->addConditonBoolOperator('AND');
        $this->column($column);
        return $this;
    }

    public function or($column)
    {
        $this->addConditonBoolOperator('OR');
        $this->column($column);
        return $this;
    }

    public function not($column)
    {
        $this->addConditonBoolOperator('NOT');
        $this->column($column);
        return $this;
    }
}
