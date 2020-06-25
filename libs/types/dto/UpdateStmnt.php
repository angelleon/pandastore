<?php

namespace PandaStore\Types\Dto;

require_once __DIR__ . "/DbStmnt.php";


use PandaStore\Interfaces\Builder;

class UpdateStmnt extends DbStmnt
{
    const FROM_CLAUSE = 0b01;
    const COLUMN_LIST = 0b10;
    const CONDITION_CLAUSE = 0b100;

    function __construct($database)
    {
        $this->state = UpdateStmnt::FROM_CLAUSE;
        $this->tables = [];
        $this->columns = [];
        $this->values = [];
        $this->conditionColumns = [];
        $this->conditionOperators = [];
        $this->conditionValues = [];
        $this->conditionBoolOperators = [];
        $this->database = is_null($database) ? "" : $database;
    }

    public function build()
    {
        return ["updateStmnt" => [
            "columns" => ["column" => $this->columns],
            "tables" => ["table" => $this->tables],
            "values" => ["value" => $this->values],
            "conditionColumns" => ["column" => $this->conditionColumns],
            "conditionOperators" => ["operator" => $this->conditionOperators],
            "conditionValues" => ["value" => $this->conditionValues],
            "conditionBoolOperators" => ["boolOperator" => $this->conditionBoolOperators],
            "database" => $this->database
        ]];
    }

    public function table($tablename)
    {
        $this->checkState(UpdateStmnt::FROM_CLAUSE);
        $this->tables[] = $tablename;
        return $this;
    }

    public function set($column)
    {
        $this->checkState(UpdateStmnt::COLUMN_LIST);
        $this->columns[] = $column;
        return $this;
    }

    public function to($value)
    {
        $this->checkState(UpdateStmnt::COLUMN_LIST);
        $this->values[] = $value;
        return $this;
    }

    public function column($column) {
        $this->checkState(UpdateStmnt::COLUMN_LIST | UpdateStmnt::CONDITION_CLAUSE);
        if ($this->state & UpdateStmnt::COLUMN_LIST) {
            $this->columns[] = $column;
        } else if ($this->state & UpdateStmnt::CONDITION_CLAUSE) {
            $this->conditionColumns[] = $column;
        }
        return $this;
    }

    private function addConditionOperator($operator)
    {
        $this->checkState(UpdateStmnt::CONDITION_CLAUSE);
        $operators = ['=', '!=', '>', '<', '>=', '<=', 'like'];
        if (!in_array($operator, $operators, true)) {
            throw new \Exception('Invalid operator');
        }
        $this->conditionOperators[] = $operator;
        return $this;
    }

    private function addConditonBoolOperator($operator)
    {
        $this->checkState(UpdateStmnt::CONDITION_CLAUSE);
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
        $this->checkState(UpdateStmnt::CONDITION_CLAUSE);
        $this->conditionOperators[] = "LIKE";
        $this->conditionValues[] = $pattern;
        return $this;
    }

    public function value($value)
    {
        $this->checkState(UpdateStmnt::COLUMN_LIST | UpdateStmnt::CONDITION_CLAUSE);
        if ($this->state & UpdateStmnt::COLUMN_LIST) {
            $this->columns[] = $value;
        } else if ($this->state & UpdateStmnt::CONDITION_CLAUSE) {
            $this->conditionValues[] = $value;
        }
        return $this;
    }

    public function where($column)
    {
        $this->checkState(UpdateStmnt::CONDITION_CLAUSE);
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
