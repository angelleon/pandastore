<?php

namespace PandaStore\View;

function renderRedirectForm($decideValue, $action, $errAction, $errCode, $errMsg)
{
    $PAGE_TITLE = "Redirecting...";
    require_once __DIR__ . "/header.php";
?>
    <form name="redirectForm" action="<?php echo ($decideValue ? $action : $errAction); ?>" method="POST">
        <?php if (!$decideValue) : ?>
            <input type="hidden" value="" name="error">
            <input type="hidden" value="<?php echo $errCode; ?>" name="code">
            <input type="hidden" value="<?php echo $errMsg; ?>" name="message">
        <?php endif; ?>
    </form>
    <script defer type="application/javascript">
        const form = document["redirectForm"];
        form.submit();
    </script>
<?php
    require_once __DIR__ . "/endDocument.php";
    exit();
}

function redirect($url = null)
{
    if (is_null($url) || strcmp($url, "") == 0) {
        $url = "/index.php";
    }
?>
    <form name="redirectForm" action="<?php echo $url ?>" method="POST">
    </form>
    <script defer type="application/javascript">
        const form = document["redirectForm"];
        form.submit();
    </script>
<?php
}

function echoPostOrEmpty($flag, $postKey)
{
    echo (array_key_exists($postKey, $_POST) && $flag) ? $_POST[$postKey] : '';
}

function redirectPost($url, $arr)
{
    if (is_null($url) || strcmp($url, "") == 0) {
        $url = "/index.php";
    }
?>
    <form name="redirectForm" action="<?php echo $url ?>" method="POST">
    <?php foreach ($arr as $key => $value):?>
        <input type="hidden" name="<?php echo $key;?>[]" value="<?php echo $value;?>">
    <?php endforeach;?>
    </form>
    <script defer type="application/javascript">
        const form = document["redirectForm"];
        form.submit();
    </script>
<?php
}
