<?php 
    require_once __DIR__.'/../Url.php';
    require_once __DIR__."/header.php";
?>
    <nav class="navbar is-primary" role="navigation" aria-label="main navigation">
      <div class="navbar-brand">
        <a class="navbar-item" href="<?php echo WEB_BASE_URL;?>/index.php">
          panda store
        </a>
        <!-- hamburguesa -->
        <a role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" data-target="storeMenubar">
          <span aria-hidden="true"></span>
          <span aria-hidden="true"></span>
          <span aria-hidden="true"></span>
        </a>
      </div>
      <div id="storeMenubar"  class="navbar-menu">
        <div class="navbar-start">
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
            Cart
          </a>
          <div class="navbar-item has-dropdown is-hoverable">
            <a class="navbar-link">
              Access
            </a>
            <div class="navbar-dropdown">
<?php if (session_status() == PHP_SESSION_NONE):?>
              <a class="navbar-item is-primary" href="<?php echo WEB_BASE_URL;?>/login.php">
                Login
              </a>
              <a class="navbar-item is-dark" href="<?php echo WEB_BASE_URL;?>/singup.php">
                Singup
              </a>
<?php else: ?>
              <a class="navbar-item is-warning" href="<?php echo WEB_BASE_URL;?>/logout.php">
                Logout
              </a>
<?php endif?>
            </div>
          </div>
        </div>
      </div>
    </nav>
