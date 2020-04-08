<?php
    require_once __DIR__."/util.php";

    if (isset($_POST["error"])) {
        $text_class = "is-danger";
    } else {
        $text_class = "";
    }
    include_once "navbar.php";
?>
<form name="frmLogin" action="<?php echo $LOGIN_FORM_PROCESSOR_URL;?>" method="POST">
    <div class="field">
        <div class="control">
            <input id="txtEmail" name="email" type="email" placeholder="Email" class="input <?php echo $text_class;?>">
        </div>
    </div>
    <div class="field">
        <div class="control">
            <input id="txtPasswd" name="passwd" type="password" placeholder="Password" class="input <?php echo $text_class;?>">
        </div>
    </div>
    <div class="field">
        <div class="control">
            <button class="button is-link" type='' onclick="return validateLoginData();">Login</button>
        </div>
    </div>
</form>
<?php
    include_once "footer.php";
?>