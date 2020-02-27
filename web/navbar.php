<?php 
    include_once 'util.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf8"/>
    <link rel="stylesheet" 
          href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.8.0/css/bulma.css"
          integrity="sha256-XF2msWsEjJwE8ORQ0exG5nFk8jDTntTMbUZKtvPRkgU=" 
          crossorigin="anonymous" />
    <!-- <link rel="stylesheet" href="<?php echo $SERVER_BASE_URL; ?>/assets/css/bulma.css"> -->
    <link rel="stylesheet" type="text/css" href="<?php echo $SERVER_BASE_URL;?>/assets/css/style.css">
    <script defer src="<?php echo $SERVER_BASE_URL;?>/assets/js/script.js"></script>
    <title>Panda store</title>
  </head>
  <body>
    <nav class="navbar is-primary">
      <div class="navbar-brand">
        <a class="navbar-item" href="<?php echo $SERVER_BASE_URL;?>/index.php">
          panda store
        </a>
        <a class="navbar-burger burger" data-target="storeMenubar">
          <span></span>
          <span></span>
          <span></span>
        </a>
      </div>
      <div class="nabvar-menu" id="storeMenubar">
        <div class="nabvar-start">
          <a class="navbar-item">
            Store0
          </a>
          <a class="navbar-item">
            Store1
          </a>
          <a class="navbar-item">
            Store2
          </a>
          <a class="navbar-item">
            Store3
          </a>
        </div>
        <div class="navbar-end">
          <a class="navbar-item">
            Car
          </a>
          <div class="navbar-item has-dropdown is-hoverable">
            <a class="navbar-link">
              Access
            </a>
            <div class="navbar-dropdown">
<?php if (session_status() == PHP_SESSION_NONE):?>
              <a class="navbar-item is-primary" href="<?php echo $SERVER_BASE_URL;?>/login.php">
                Login
              </a>
              <a class="navbar-item is-dark" href="<?php echo $SERVER_BASE_URL;?>/singup.php">
                Singup
              </a>
<?php else: ?>
              <a class="navbar-item is-warning" href="<?php echo $SERVER_BASE_URL;?>/logout.php">
                Logout
              </a>
<?php endif?>
            </div>
          </div>
        </div>
      </div>
    </nav>
