<?php
    require_once __DIR__.'/../libs/view/header.php';

    if (session_status() == PHP_SESSION_ACTIVE) {
        header("Location: ".WEB_BASE_URL);
        exit();
    }

    require_once __DIR__.'/../libs/view/View.php';
    require_once __DIR__.'/../libs/view/navbar.php';
    require_once __DIR__.'/../libs/clients/SingupClient.php';

    use PandaStore\Clients\SingupClient;
    use function PandaStore\View\echoPostOrEmpty;

    if ($_SERVER['REQUEST_METHOD'] == 'POST' &&
        isset($_POST, $_POST["code"], $_POST["msg"])) {
        $code = $_POST["code"];
        $msg = $_POST["msg"];
        $errFlag = true;
    } else {
        $code = SingupClient::ERR_NO_ERROR;
        $errFlag = false;
        $msg = '';
    }
?>

<div class='box'>
    <h3 class='title'>Singup</h3>
    <form name="singup" action="<?php echo SINGUP_FORM_PROCESSOR_URL;?>" method="POST">
        <div class="field">
            <div class="control has-icons-left has-icons-right <?echo $code == SingupClient::ERR_INVALID_GIVEN_NAME ? 'is-danger' : ''?>">
                <input type="text" placeholder="Given name" name="givenName" class="input" id="txtGivenName"
                       value="<?php echoPostOrEmpty($errFlag, "givenName");?>">
                <span class="icon is-small is-left">
                    <i class="fas fa-user"></i>
                </span>
            </div>
        </div>
        <div class="field">
            <div class="control has-icons-left">
                <input type="text" placeholder="Surname" name="surname" class="input" id="txtSurname"
                       value="<?php echoPostOrEmpty($errFlag, "surname");?>">
                <span class='icon is-small is-left'>
                    <i class="fas fa-users"></i>
                </span>
            </div>
        </div>
        <div class="field">
            <div class="control has-icons-left has-icons-right">
                <input type="email" placeholder="Email" name="email" class="input" id="txtEmail"
                       value="<?php echoPostOrEmpty($errFlag, "email");?>">
                <span class='icon is-small is-left'>
                    <i class="far fa-envelope"></i>
                </span>
            </div>
        </div>
        <div class="field">
            <div class="control has-icons-left has-icons-right">
                <input type="password" placeholder="Password" name="passwd" class="input" id="txtPasswd">
                <span class='icon is-small is-left'>
                    <i class="fas fa-key"></i>
                </span>
                <div onmousedown="showPasswd('txtPasswd');" onmouseup="hidePasswd('txtPasswd');" onmouseleave="hidePasswd('txtPasswd');" class="dbg">Show password</div>
            </div>
        </div>
        <div class="field">
            <div class="control has-icons-left has-icons-right">
                <input type="password" placeholder="Confirm password" class="input" id="txtConfirmPasswd">
                <span class='icon is-small is-left'>
                    <i class="fas fa-key"></i>
                </span>
                <div onmousedown="showPasswd('txtConfirmPasswd');" onmouseup="hidePasswd('txtConfirmPasswd');" onmouseleave="hidePasswd('txtConfirmPasswd');" class="dbg">Show password</div>
            </div>
        </div>
        <div class="field">
            <div class="control">
                <button class="button is-link" type="" onclick="return validateSingupData();">Singup</button>
            </div>
        </div>
    </form>
</div>

<?php
    require_once __DIR__.'/../libs/view/footer.php';
