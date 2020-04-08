<?php
    namespace PandaStore\Types;
    require_once __DIR__."/SelectStatement.php";
    require_once __DIR__."/InsertStatement.php";
    require_once __DIR__."/UpdateStatement.php";
    require_once __DIR__."/DeleteStatement.php";

    //use SelectStatement;
    //use InsertStatement;
    //use UpdateStatement;
    //use DeleteStatement;

    class StmntBuilder {
        public static function select($column) {
            return (new SelectStatement())->column($column);
        }

        public static function insert($column) {
            if (is_null($column)) {
                return new InsertStatement();
            } else {
                return (new InsertStatement())->column($column);
            }
        }

        public static function update($table) {
            return (new UpdateStatement())->table($table);
        }

        public static function delete($column) {
            if (is_null($column)) {
                return new DeleteStatement();
            } else {
                return (new DeleteStatement())->column($column);
            }
        }
    }