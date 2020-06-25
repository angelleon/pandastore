<?php
  require_once __DIR__."/../Url.php";
  if (!isset($PAGE_TITLE)) {
    $PAGE_TITLE = "";
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf8"/>
    <!-- bulma css -->
    <link rel="stylesheet" 
          href="https://cdn.jsdelivr.net/npm/bulma@0.8.2/css/bulma.css"
          integrity="sha256-8BrtNNtStED9syS9F+xXeP815KGv6ELiCfJFQmGi1Bg="
          crossorigin="anonymous">
    <!-- fonts awsome -->
    <script src="https://kit.fontawesome.com/4e0f88479e.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="<?php echo CSS_URL;?>/style.css">
    <link rel="shortcut icon" type="image/png" href="<?php echo IMG_URL;?>/favicon.png" />
    <script defer src="<?php echo JS_URL;?>/script.js"></script>
    <title><?php echo strlen($PAGE_TITLE) > 0 ? $PAGE_TITLE." - " : ""; ?>Pandastore</title>
  </head>
  <body>