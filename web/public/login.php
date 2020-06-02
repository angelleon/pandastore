<?php
    require_once __DIR__."/../libs/Url.php";

    if (isset($_POST["error"])) {
        $text_class = "is-danger";
    } else {
        $text_class = "";
    }
    $PAGE_TITLE = "Login";
    require_once __DIR__."/../libs/view/navbar.php";
?>
<div class="box">
    <h3 class="title">Login</h3>
    <form name="frmLogin" action="<?php echo LOGIN_FORM_PROCESSOR_URL;?>" method="POST">
        <div class="field">

            <div class="control has-icons-left">
                <input id="txtEmail" name="email" type="email" placeholder="Email" class="input">
                <span class="icon is-small is-left">
                    <i class="fas fa-envelope"></i>
                </span>
            </div>
            
        </div>
        <div class="field">
            <div class="control has-icons-left has-icons-right">
                <input id="txtPasswd" name="passwd" type="password" placeholder="Password" class="input <?php echo $text_class;?>">
                <span class="icon is-small is-left">
                    <i class="fas fa-key"></i>
                </span>
                <span class="icon is-small is-right">
                    <i class="far fa-eye" onmousedown="showPasswd('txtPasswd');" onmouseup="hidePasswd('txtPasswd');" onmouseleave="hidePasswd('txtPasswd');"></i>
                </span>
            </div>
        </div>
        <div class="field">
            <div class="control">
                <button class="button is-link" type='' onclick="return validateLoginData();">Login</button>
            </div>
        </div>
    </form>
</div>
<?php
    require_once __DIR__."/../libs/view/footer.php";
?>