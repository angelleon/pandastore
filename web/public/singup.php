<?php
    if (session_status() == PHP_SESSION_ACTIVE) {
        header("Location: $WEB_BASE_URL");
        exit();
    }

    include_once 'navbar.php';
?>

<form name="singup" action="<?php echo $SINGUP_FORM_PROCESSOR_URL;?>" method="POST">
    <div class="field">
        <div class="control">
            <input type="text" placeholder="Given name" name="givenName" class="input" id="txtGivenName">
        </div>
    </div>
    <div class="field">
        <div class="control">
            <input type="text" placeholder="Surname" name="surname" class="input" id="txtSurname">
        </div>
    </div>
    <div class="field">
        <div class="control has-icons-left has-icons-right">
            <input type="email" placeholder="Email" name="email" class="input" id="txtEmail">
        </div>
    </div>
    <div class="field">
        <div class="control has-icons-left has-icons-right">
            <input type="password" placeholder="Password" name="passwd" class="input" id="txtPasswd">
            <div onmousedown="showPasswd('txtPasswd');" onmouseup="hidePasswd('txtPasswd');" onmouseleave="hidePasswd('txtPasswd');" class="dbg">Show password</div>
        </div>
    </div>
    <div class="field">
        <div class="control has-icons-left has-icons-right">
            <input type="password" placeholder="Confirm password" class="input" id="txtConfirmPasswd">
            <div onmousedown="showPasswd('txtConfirmPasswd');" onmouseup="hidePasswd('txtConfirmPasswd');" onmouseleave="hidePasswd('txtConfirmPasswd');" class="dbg">Show password</div>
        </div>
    </div>
    <div class="field">
        <div class="control">
            <button class="button is-link" type="" onclick="return validateSingupData();">Singup</button>
        </div>
    </div>
</form>

<?php
    include_once 'footer.php';
