<?php

namespace PandaStore\Types\Dto;

require_once __DIR__ . "/SelectStmnt.php";
require_once __DIR__ . "/InsertStmnt.php";
require_once __DIR__ . "/UpdateStmnt.php";
require_once __DIR__ . "/DeleteStmnt.php";

//use SelectStmnt;
//use InsertStmnt;
//use UpdateStmnt;
//use DeleteStmnt;

class StmntBuilder
{
    private static $database = null;

    public static function setDatabase($database)
    {
        if (is_null($database) || !is_string($database)) {
            throw new \Exception("Invalid value for database parameter");
        }
        StmntBuilder::$database = $database;
    }

    public static function select($column)
    {
        return (new SelectStmnt(StmntBuilder::$database))->column($column);
    }

    public static function insert($column)
    {
        if (is_null($column)) {
            return new InsertStmnt();
        } else {
            return (new InsertStmnt(StmntBuilder::$database))->column($column);
        }
    }

    public static function update($table)
    {
        return (new UpdateStmnt(StmntBuilder::$database))->table($table);
    }

    public static function delete()
    {
        return new DeleteStmnt(StmntBuilder::$database);
    }
}
