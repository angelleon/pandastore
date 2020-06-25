<?php
    namespace PandaStore\Util;

    class Encrypter {
        static function encrypt(string $s) {
            return hash("sha3-256", $s);
        }
    }