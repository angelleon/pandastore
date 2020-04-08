<?php
    namespace PandaStore\Util;

    class Encrypter {
        function encrypt(string $s) {
            return hash("sha3-256", $s);
        }
    }