<?php
  function renderRedirectForm($decideValue, $action, $errAction, $errCode, $errMsg) {
    ?>
      <form name="redirectForm" action="<?php echo ($decideValue ? $action : $errAction);?>" method="POST">
        <?php if ($decideValue): ?>
          <input type="hidden" value="" name="error">
          <input type="hidden" value="<?php echo $errCode;?>" name="code">
          <input type="hidden" value="<?php echo $errMsg;?>" name="message">
        <?php endif; ?>
      </form>
      <script defer type="application/javascript">
        let form = document["redirectForm"];
        form.submit();
      </script>
    <?php
  }
