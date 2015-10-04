<?php include "header.php"; ?>
<h1 class="row">Purchases</h1>
<?php
if(!$current_user)
{
  $_SESSION["notice"] = "Please login to continue";
  header("Location: index.php");
  die();
}
$result = mysqli_query($con, "SELECT * FROM purchases WHERE user_id = $id ORDER BY created_at DESC");
?>
<?php while($purchase = mysqli_fetch_assoc($result)): ?>
<div class="row panel">
<h4><strong>Date: <?= $purchase["created_at"]; ?></strong></h4>
<?php
$purchase_id = $purchase["id"];
$result2 = mysqli_query($con, "SELECT * FROM purchase_products pp INNER JOIN products p ON p.id = pp.product_id WHERE pp.purchase_id = $purchase_id");
$total = 0;
?>
<div class="row">
  <strong>
    <div class="small-2 columns">&nbsp;</div>
    <div class="small-4 columns">Name</div>
    <div class="small-1 columns">Quantity</div>
    <div class="small-2 columns">Price Per Item</div>
    <div class="small-1 columns">Total</div>
    <div class="small-1 columns">&nbsp;</div>
  </strong>
</div>
<br>
<?php while($product = mysqli_fetch_assoc($result2)): $total += $product["price"] * $product["quantity"] ?>
<div class="row">
  <div class="small-2 columns"><img src="<?= $product["image"] ? $uploads["files"] . "products/" . $product["image"] : "img/placeholder.png"; ?>" width="40%"></div>
  <div class="small-4 columns"><?= $product["name"]; ?></div>
  <div class="small-1 columns"><?= $product["quantity"]; ?></div>
  <div class="small-2 columns">£<?= $product["price"]; ?></div>
  <div class="small-1 columns">£<?= $product["price"] * $product["quantity"]; ?></div>
  <div class="small-1 columns"><i class="fi-check success"></i></div>
</div>
<?php endwhile; ?>
<hr>
<div class="row">
  <strong>
    <div class="small-2 columns">&nbsp;</div>
    <div class="small-4 columns">&nbsp;</div>
    <div class="small-1 columns">&nbsp;</div>
    <div class="small-2 columns">&nbsp;</div>
    <div class="small-1 columns"><?= "£$total" ?></div>
    <div class="small-1 columns">&nbsp;</div>
  </strong>
</div>
</div>
<?php endwhile; ?>
<?php include "footer.php"; ?>
