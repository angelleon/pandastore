<?php
    if (isset($_POST["operation"])) {
        if ($_POST["operation"] == $MK_PRODUCT) {
            if (isset($_FILES["photo"])) {
                $tmp_name = $_FILES["photo"]["tmp_name"];
                $name = $_FILES["photo"]["name"];
                $extension = pathinfo($name, PATHINFO_EXTENSION);
                if (preg_match("/(jp[e]?g|png)/i", $extension) != 1) {
                    echo "Unsupported file $extension";
                    die();
                }
                $data = file_get_contents($tmp_name);
                $hash_str = hash("sha3-256", $data);
                move_uploaded_file($tmp_name, __DIR__."/../data/$hash_str.$extension");
            }
        } else if ($_POST["operation"] == $VI_PRODUCT) {

        } else if ($_POST["operation"] == $RM_PRODUCT) {

        } else if ($_POST["operation"] == $LS_PRODUCT) {

        }

        
    }