<?php
    require_once __DIR__."/navbar.php";
    ?>

    <form name="productForm" method="POST" enctype="multipart/form-data" action="./services/productManagerService.php">
        <input type="text" name="name" placeholder="Product name">
        <input type="number" step="0.01" name="price" placeholder="Price">
        <input type="file" name="photo" placeholder="Photo">
        <input type="text" name="description" placeholder="Description">
        <input type="text" name="tags" placeholder="Tags">
        <button type="none" onClick="return checkProductInfo();">Agregar</button>
    </form>

<?php
    require_once __DIR__."footer.php";