<?php include "header.php"; ?>
<script type="text/javascript">
$(document).ready(function() {
  $("[type=number]").keypress(function (e) {
      e.preventDefault();
  });
});
function changeQuantity(product_id) {
  $.post("cart.php", $("#product" + product_id).serialize(), function(data) {
    location.href = "cart.php";
    return false;
  });
}
</script>
<?php
if(!$current_user)
{
  $_SESSION["notice"] = "Please login to continue";
  header("Location: index.php");
  die();
}
if(isset($_POST["change_quantity"]))
{
  $product_id = $_POST["product_id"];
  $quantity = $_POST["quantity"];
  $result = mysqli_query($con, "SELECT quantity FROM cart WHERE user_id = $id AND product_id = $product_id");
  $product = mysqli_fetch_assoc($result);
  $diff = $quantity - $product["quantity"];
  mysqli_query($con, "UPDATE cart SET quantity = $quantity WHERE user_id = $id AND product_id = $product_id");
  mysqli_query($con, "UPDATE products SET stock = stock - $diff WHERE id = $product_id");
}
if(isset($_POST["remove"]))
{
  $product_id = $_POST["product_id"];
  $product_num = $_POST["product_num"];
  mysqli_query($con, "DELETE FROM cart WHERE user_id = $id AND product_id = $product_id");
  //mysqli_query($con, "UPDATE products SET stock = stock + $product_num WHERE id = $product_id");
  $_SESSION["notice"] = "Product removed successfully";
  header("Location: cart.php");
  die();
}
if(isset($_POST["checkout"]))
{
  mysqli_query($con, "INSERT INTO purchases VALUES(NULL, $id, NULL)");
  $purchase_id = mysqli_insert_id($con);
  $result = mysqli_query($con, "SELECT * FROM cart WHERE user_id = $id");
  while($product = mysqli_fetch_assoc($result))
  {
    $product_id = $product["product_id"];
    $quantity = $product["quantity"];
    mysqli_query($con, "INSERT INTO purchase_products VALUES(NULL, $purchase_id, $product_id, $quantity)");
      mysqli_query($con, "UPDATE products SET stock = stock - $quantity WHERE id = $product_id");
  }
  mysqli_query($con, "DELETE FROM cart WHERE user_id = $id");
  $_SESSION["notice"] = "Items will be delivered in 3 working days";
  header("Location: index.php");
  die();
}
$result = mysqli_query($con, "SELECT * FROM cart INNER JOIN products p ON p.id = product_id WHERE user_id = $id");
$total = 0;
?>
<h1 class="row">Cart</h1>
<div class="row">
  <strong>
    <div class="small-2 columns">&nbsp;</div>
    <div class="small-4 columns">Name</div>
    <div class="small-1 columns">Quantity</div>
    <div class="small-2 columns">Price Per Item</div>
    <div class="small-1 columns">Total</div>
    <div class="small-1 columns">Remove</div>
  </strong>
</div>
<br>
<?php while($product = mysqli_fetch_assoc($result)): $total += $product["price"] * $product["quantity"]; ?>
<div class="row">
  <div class="small-2 columns"><img src="<?= $product["image"] ? $uploads["files"] . "products/" . $product["image"] : "img/placeholder.png"; ?>" width="40%"></div>
  <div class="small-4 columns"><?= $product["name"]; ?></div>
  <div class="small-1 columns">
    <form id="product<?= $product["id"]; ?>" onsubmit="return false;">
      <input type="hidden" name="change_quantity" value="true">
      <input type="hidden" name="product_id" value="<?= $product["product_id"]; ?>">
      <input type="number" name="quantity" value="<?= $product["quantity"]; ?>" value="1" min="1" max="<?= $product["stock"] + $product["quantity"]; ?>" onchange="return changeQuantity(<?= $product["id"]; ?>)">
    </form>
  </div>
  <div class="small-2 columns">$<?= $product["price"]; ?></div>
  <div class="small-1 columns">$<?= $product["price"] * $product["quantity"]; ?></div>
  <div class="small-1 columns">
    <form method="post">
      <input type="hidden" name="remove" value="true">
      <input type="hidden" name="product_num" value="<?= $product["quantity"]; ?>">
      <input type="hidden" name="product_id" value="<?= $product["product_id"]; ?>">
      <button type="submit" class="tiny alert round button fi-x"></button>
    </form>
  </div>
</div>
<?php endwhile; ?>
<hr>
<div class="row">
  <strong>
    <div class="small-2 columns">&nbsp;</div>
    <div class="small-2 columns">&nbsp;</div>
    <div class="small-1 columns">&nbsp;</div>
    <div class="small-4 columns">&nbsp;</div>
    <div class="small-1 columns"><?= "\$$total" ?></div>
    <div class="small-2 columns">
      <form method="post" onsubmit="return confirm('Are you sure you want to checkout?');">
        <input type="hidden" name="checkout" value="true">
        <?php if($total > 0): ?>
          <button type="submit" class="tiny success button"><i class="fi-arrow-right"></i> Checkout</button>
        <?php endif; ?>
      </form>
    </div>
  </strong>
</div>
<?php include "footer.php"; ?>
