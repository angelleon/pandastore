<?php
    $chars = ' !"#$%&\'()*+,-./\:;<=>?@[]^_`{|}~]';
    for ($i = 0; $i < strlen($chars); $i++) {
        echo "$chars[$i] ".preg_match('/[ !"#$%&\'()*+,\-.\/:;<=>?@[\]^_`{|}~]/', $chars[$i])."<br>\n";
        echo "$chars[$i] ".preg_match('/\\\/', $chars[$i])."<br>\n";
    }