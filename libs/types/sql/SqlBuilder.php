<?php

namespace Pandastore\Types\Sql;

require_once __DIR__ . "/../../vendor/autoload.php";

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;


class SqlBuilder
{
    public static function select($selectStmnt, &$query, &$values)
    {
        $formatter = new LineFormatter(null, null, true, true); // allows witting LF in log entries
        $handler = new StreamHandler(__DIR__ . "/../../../logs/" . basename(__FILE__) . ".log", Logger::DEBUG);
        $handler->setFormatter($formatter);
        $log = new Logger(basename(__FILE__));
        $log->pushHandler($handler);
        $log->debug("Starting sql query construction");
        $log->debug(json_encode($selectStmnt, JSON_PRETTY_PRINT));
        // query construction
        $query .= "SELECT ";



        if (is_null($selectStmnt["columns"]["column"])) {
            return ["code" => -1, "msg" => "You must specify at least one column to select"];
        }
        if (is_array($selectStmnt["columns"]["column"])) {
            $columns = $selectStmnt["columns"]["column"];
            if (count($columns) == 0) {
                return ["code" => -1, "msg" => "You must specify at least one column to select"];
            }
            for ($i = 0; $i < count($columns); $i++) {
                $query .= $columns[$i];
                if ($i < count($columns) - 1) {
                    $query .= ', ';
                }
            }
        } else {
            $query .= $selectStmnt["columns"]["column"];
        }

        $query .= " FROM ";

        if (is_null($selectStmnt["tables"]["table"])) {
            return ["code" => 0, "msg" => "You should specify at least one table to select from"];
        }
        if (is_array($selectStmnt["tables"]["table"])) {
            $tables = $selectStmnt["tables"]["table"];
            if (count($tables) < 1) {
                return ["code" => -1, "msg" => "You must specify at least one table to select from"];
            } /*else if (count($tables) > 1) {
                return ["code" => -1, "msg" => "You must specify at most one table to select from"];
            }*/
            //$query .= $tables[0];
            for ($i=0; $i < count($tables); $i++) {
                $query .= $tables[$i];
                if ($i < count($tables) - 1) {
                    $query .= " ,";
                }
            }
        } else {
            $query .= $selectStmnt["tables"]["table"];
        }

        if (is_null($selectStmnt["conditionColumns"]["column"])) {
            $thereIsWhere = false;
        } else if (is_array($selectStmnt["conditionColumns"]["column"])) {

            $columns = $selectStmnt["conditionColumns"]["column"];
            $thereIsWhere = true;
        } else {
            $columns = [$selectStmnt["conditionColumns"]["column"]];
            $thereIsWhere = true;
        }

        if (is_null($selectStmnt["conditionOperators"]["operator"])) {
        } else if (is_array($selectStmnt["conditionOperators"]["operator"])) {
            $operators = $selectStmnt["conditionOperators"]["operator"];
        } else {
            $operators = [$selectStmnt["conditionOperators"]["operator"]];
        }


        if (
            is_null($selectStmnt["conditionBoolOperators"])
            || !is_array($selectStmnt["conditionBoolOperators"])
        ) {
            $boolOperators = [];
        } else if (is_array($selectStmnt["conditionBoolOperators"]["boolOperator"])) {
            $boolOperators = $selectStmnt["conditionBoolOperators"]["boolOperator"];
        } else {
            $boolOperators = [$selectStmnt["conditionBoolOperators"]["boolOperator"]];
        }
        if (is_null($selectStmnt["conditionValues"]["value"])) {
        } else if (is_array($selectStmnt["conditionValues"]["value"])) {
            $values = $selectStmnt["conditionValues"]["value"];
        } else {
            $values = [$selectStmnt["conditionValues"]["value"]];
        }

        if (count($operators) != count($columns)) {
            return ["code" => -1, "msg" => "You must specify enought relational operators for where condition"];
        } else if (count($columns) > 1 && count($boolOperators) + 1 != count($columns)) {
            return ["code" => -1, "msg" => "You must specify enought boolean operators for where condition"];
        } else if (count($values) != count($columns)) {
            return ["code" => -1, "msg" => "You must specify enought values to bind for where condition"];
        }

        if ($thereIsWhere)
            $query .= " WHERE ";
        for ($i = 0; $i < count($columns); $i++) {
            $query .= $columns[$i] . " ";
            $query .= $operators[$i] . " ";
            $query .= "? ";
            if ($i < count($columns) - 1) {
                $query .= $boolOperators[$i] . " ";
            }
        }

        if (array_key_exists("limitCount", $selectStmnt)) {
            $query .= " LIMIT ";
            if (!array_key_exists("limitOffset", $selectStmnt)) {
                $query .= $selectStmnt["limitOffset"] . ", ";
            }
            $query .= $selectStmnt["limitCount"];
        }
        $log->debug("returning from query builder");
    }

    public static function insert($insertStmnt, &$query, &$values)
    {
        $formatter = new LineFormatter(null, null, true, true); // allows witting LF in log entries
        $handler = new StreamHandler(__DIR__ . "/../../../logs/" . basename(__FILE__) . ".log", Logger::DEBUG);
        $handler->setFormatter($formatter);
        $log = new Logger(basename(__FILE__));
        $log->pushHandler($handler);
        $log->debug("Starting sql query construction");
        $log->debug(json_encode($insertStmnt, JSON_PRETTY_PRINT));

        // query construction
        $query = "INSERT INTO ";
        if (is_null($insertStmnt["tables"]["table"])) {
            //throw new \Exception();
            return ["code" => -1, "msg" => "You must specify at least one table to insert into"];
        }
        if (is_array($insertStmnt["tables"]["table"])) {
            $tables = $insertStmnt["tables"]["table"];
            throw new \Exception();
            if (count($tables) < 1) {
                //throw new Exception();
                return ["code" => -1, "msg" => "You must specify at least one table to insert into"];
            } else if (count($tables) > 1) {
                //throw new \Exception();
                return ["code" => -1, "msg" => "You must specify at most one table to insert into"];
            }
            $query .= $tables[0];
        } else {
            //throw new \Exception();
            $query .= $insertStmnt["tables"]["table"];
        }

        if (is_null($insertStmnt["columns"]["column"])) {
            $query .= " ";
        } else if (is_array($insertStmnt["columns"]["column"])) {
            $columns = $insertStmnt["columns"]["column"];
            $query .= "(";
            for ($i = 0; $i < count($columns); $i++) {
                $query .= $columns[$i];
                if ($i < count($columns) - 1) {
                    $query .= ", ";
                }
            }
            $query .= ") ";
        } else {
            $query .= "(" . $insertStmnt["columns"]["column"] . ") ";
        }

        $query .= "VALUES ( ";

        if (is_null($insertStmnt["values"]["value"])) {
            return ["code" => -1, "msg" => "You must specify at least one value to insert"];
        }
        if (is_array($insertStmnt["values"]["value"])) {
            $inValues = $insertStmnt["values"]["value"];
            if (count($inValues) == 0) {
                return ["code" => -1, "msg" => "You must specify at least one value to insert"];
            }
            //$query .= $selectStmnt["conditionValues"]["value"];
            for ($i = 0; $i < count($inValues); $i++) {
                $query .= "? ";
                $values[] = $inValues[$i];
                if ($i < count($inValues) - 1) {
                    $query .= ", ";
                }
            }
        } else {
            $query .= "? ";
            $values[] = $insertStmnt["values"]["value"];
        }
        $query .= ")";
    }



    public static function update($updateStmnt, &$query, &$values)
    {
        $formatter = new LineFormatter(null, null, true, true); // allows witting LF in log entries
        $handler = new StreamHandler(__DIR__ . "/../../../logs/" . basename(__FILE__) . ".log", Logger::DEBUG);
        $handler->setFormatter($formatter);
        $log = new Logger(basename(__FILE__));
        $log->pushHandler($handler);
        $log->debug("Starting sql update query construction");
        $log->debug(json_encode($updateStmnt, JSON_PRETTY_PRINT));
        $query .= "SELECT CURRENT_TIMESTAMP";

        $query = "UPDATE ";
        if (is_null($updateStmnt["tables"]["table"])) {
            //throw new \Exception();
            return ["code" => -1, "msg" => "You must specify at least one table to update"];
        }
        if (is_array($updateStmnt["tables"]["table"])) {
            $tables = $updateStmnt["tables"]["table"];
            if (count($tables) < 1) {
                //throw new Exception();
                return ["code" => -1, "msg" => "You must specify at least one table to update"];
            } else if (count($tables) > 1) {
                //throw new \Exception();
                return ["code" => -1, "msg" => "You must specify at most one table to update"];
            }
            $query .= $tables[0];
        } else {
            //throw new \Exception();
            $query .= $updateStmnt["tables"]["table"];
        }

        // SET 

        if (is_null($updateStmnt["columns"]["column"])) {
            return ["code" => -1, "msg" => "You must specify at least one column to update"];
        }



        if (is_array($updateStmnt["columns"]["column"])) {
            $columns = $updateStmnt["columns"]["column"];
            if (count($columns) == 0) {
                return ["code" => -1, "msg" => "You must specify at least one column to select"];
            }
        } else {
            $columns = [$updateStmnt["columns"]["column"]];
        }

        if (is_null($updateStmnt["values"]["value"])) {
        } else if (is_array($updateStmnt["values"]["value"])) {
            $setValues = $updateStmnt["values"]["value"];
        } else {
            $setValues = [$updateStmnt["values"]["value"]];
        }

        if (count($columns) != count($setValues)) {
            return ["code" => -1, "msg" => "You must provide same number of columns and values to set"];
        }

        $query .= " SET ";
        for ($i = 0; $i < count($columns); $i++) {
            $query .= $columns[$i] . " = ?";
            $values[] = $setValues[$i];
            if ($i < count($columns) - 1) {
                $query .= ' , ';
            }
        }
        $numUpdtColumns = count($columns);
        //$query .= "";
        if (!array_key_exists("conditionColumns", $updateStmnt)) {
            $thereIsWhere = false;
        } else if (is_array($updateStmnt["conditionColumns"]["column"])) {

            $columns = $updateStmnt["conditionColumns"]["column"];
            $thereIsWhere = true;
        } else {
            $columns = [$updateStmnt["conditionColumns"]["column"]];
            $thereIsWhere = true;
        }

        

        if (!$thereIsWhere) {
            return ["code" => 0, "msg" => "sql update statement built without where clause"];
        }

        if (!array_key_exists("conditionOperators", $updateStmnt) || 
            is_null($updateStmnt["conditionOperators"]["operator"])) {
                return ["code" => -1, "msg" => "you must specify equal number of colums and vales to compate to in where clause"];
        } else if (is_array($updateStmnt["conditionOperators"]["operator"])) {
            $operators = $updateStmnt["conditionOperators"]["operator"];
        } else {
            $operators = [$updateStmnt["conditionOperators"]["operator"]];
        }


        if (!array_key_exists("conditionBoolOperators", $updateStmnt)
            || !is_array($updateStmnt["conditionBoolOperators"])
        ) {
            $boolOperators = [];
        } else if (is_array($updateStmnt["conditionBoolOperators"]["boolOperator"])) {
            $boolOperators = $updateStmnt["conditionBoolOperators"]["boolOperator"];
        } else {
            $boolOperators = [$updateStmnt["conditionBoolOperators"]["boolOperator"]];
        }

        if (is_null($updateStmnt["conditionValues"]["value"])) {
        } else if (is_array($updateStmnt["conditionValues"]["value"])) {
            $inValues = $updateStmnt["conditionValues"]["value"];
        } else {
            $inValues = [$updateStmnt["conditionValues"]["value"]];
        }

        if (count($operators) != count($columns) || count($columns) != count($inValues)) {
            return ["code" => -1, "msg" => "You must specify enought relational operators for where condition"];
        } else if (count($columns) > 1 && count($boolOperators) + 1 != count($columns)) {
            return ["code" => -1, "msg" => "You must specify enought boolean operators for where condition"];
        } else if (count($inValues) + count($values) != count($columns) + $numUpdtColumns) {
            return ["code" => -1, "msg" => "You must specify enought values to bind for where condition"];
        }

        if ($thereIsWhere)
            $query .= " WHERE ";
        for ($i = 0; $i < count($columns); $i++) {
            $query .= $columns[$i] . " ";
            $query .= $operators[$i] . " ";
            $query .= "?";
            $values[] = $inValues[$i];
            if ($i < count($columns) - 1) {
                $query .= " " . $boolOperators[$i] . " ";
            }
        }

        $log->debug("returning from query builder");
    }

    public static function delete($deleteStmnt, &$query, &$values)
    {
        $formatter = new LineFormatter(null, null, true, true); // allows witting LF in log entries
        $handler = new StreamHandler(__DIR__ . "/../../../logs/" . basename(__FILE__) . ".log", Logger::DEBUG);
        $handler->setFormatter($formatter);
        $log = new Logger(basename(__FILE__));
        $log->pushHandler($handler);
        $log->debug("Starting sql query construction");
        $log->debug(json_encode($deleteStmnt, JSON_PRETTY_PRINT));

        $query = "DELETE FROM ";
        if (is_null($deleteStmnt["tables"]["table"])) {
            //throw new \Exception();
            return ["code" => -1, "msg" => "You must specify at least one table to update"];
        }
        if (is_array($deleteStmnt["tables"]["table"])) {
            $tables = $deleteStmnt["tables"]["table"];
            if (count($tables) < 1) {
                //throw new Exception();
                return ["code" => -1, "msg" => "You must specify at least one table to update"];
            } else if (count($tables) > 1) {
                //throw new \Exception();
                return ["code" => -1, "msg" => "You must specify at most one table to update"];
            }
            $query .= $tables[0];
        } else {
            //throw new \Exception();
            $query .= $deleteStmnt["tables"]["table"];
        }

        // SET 

        if (is_null($deleteStmnt["conditionColumns"]["column"])) {
            return ["code" => -1, "msg" => "You must specify at least one column to update"];
        }


        if (!array_key_exists("conditionColumns", $deleteStmnt)) {
            $thereIsWhere = false;
        } else if (is_array($deleteStmnt["conditionColumns"]["column"])) {

            $columns = $deleteStmnt["conditionColumns"]["column"];
            $thereIsWhere = true;
        } else {
            $columns = [$deleteStmnt["conditionColumns"]["column"]];
            $thereIsWhere = true;
        }

        if (!$thereIsWhere) {
            return;
        }

        if (is_null($deleteStmnt["conditionOperators"]["operator"])) {
        } else if (is_array($deleteStmnt["conditionOperators"]["operator"])) {
            $operators = $deleteStmnt["conditionOperators"]["operator"];
        } else {
            $operators = [$deleteStmnt["conditionOperators"]["operator"]];
        }


        if (
            is_null($deleteStmnt["conditionBoolOperators"])
            || !is_array($deleteStmnt["conditionBoolOperators"])
        ) {
            $boolOperators = [];
        } else if (is_array($deleteStmnt["conditionBoolOperators"]["boolOperator"])) {
            $boolOperators = $deleteStmnt["conditionBoolOperators"]["boolOperator"];
        } else {
            $boolOperators = [$deleteStmnt["conditionBoolOperators"]["boolOperator"]];
        }
        if (is_null($deleteStmnt["conditionValues"]["value"])) {
        } else if (is_array($deleteStmnt["conditionValues"]["value"])) {
            $values = $deleteStmnt["conditionValues"]["value"];
        } else {
            $values = [$deleteStmnt["conditionValues"]["value"]];
        }

        if (count($operators) != count($columns)) {
            return ["code" => -1, "msg" => "You must specify enought relational operators for where condition"];
        } else if (count($columns) > 1 && count($boolOperators) + 1 != count($columns)) {
            return ["code" => -1, "msg" => "You must specify enought boolean operators for where condition"];
        } else if (count($values) != count($columns)) {
            return ["code" => -1, "msg" => "You must specify enought values to bind for where condition"];
        }

        if ($thereIsWhere)
            $query .= " WHERE ";
        for ($i = 0; $i < count($columns); $i++) {
            $query .= $columns[$i] . " ";
            $query .= $operators[$i];
            $query .= " ?";
            if ($i < count($columns) - 1) {
                $query .= " " . $boolOperators[$i] . " ";
            }
        }

        $log->debug("returning from query builder");
    }
}
