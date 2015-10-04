<?php error_reporting(E_ALL); ini_set('display_errors', 1); ?>
<?php include "header.php" ?>
<?php
if(isset($_POST["cart"]))
{
  $product_id = $_POST["product_id"];
  $product_num = $_POST["number"];
  $result = mysqli_query($con, "SELECT * FROM cart WHERE user_id = $id AND product_id = $product_id");
  $exists = mysqli_fetch_assoc($result);
  if($exists) mysqli_query($con, "UPDATE cart set quantity = quantity + $product_num where user_id = $id AND product_id = $product_id");
  else mysqli_query($con, "INSERT INTO cart VALUES(NULL, $id, $product_id, $product_num)");
  mysqli_query($con, "UPDATE products SET stock = stock - $product_num WHERE id = $product_id");
  $_SESSION["notice"]="Item added to cart successfully";
  header("Location: index.php");
  die();
}
?>
<div class="row" data-equalizer>
<h1>Products</h1>
<?php
$result = mysqli_query($con, "SELECT * FROM products");
while($product = mysqli_fetch_assoc($result)):
?>
<div class="small-12 medium-6 large-4 columns">
  <div data-equalizer-watch>
    <img src="<?= $product["image"] ? $uploads["files"] . "products/" . $product["image"] : "img/placeholder.png"; ?>" width="100%">
  </div>
  <div class="row">
    <div class="small-4 columns"><strong><?php echo $product["name"]; ?></strong><br><i>£<?php echo $product["price"]; ?></i></div>
    <?php if($product["stock"] && $current_user): ?>
      <form method="post">
        <input type="hidden" name="cart" value="true">
        <input type="hidden" name="product_id" value="<?= $product["id"]; ?>">
        <div class="small-3 columns"><input name="number" type="number" value="1" min="1" max="<?php echo $product["stock"]; ?>"></div>
        <div class="small-5 columns"><button type="submit" class="right tiny success button"><i class="fi-shopping-cart"></i> Add to Cart</button></div>
      </form>
    <?php elseif(!$current_user): ?>
      <div class="small-8 columns"><a href="javascript:;" class="disabled right tiny success button">Please Login to buy</a></div>
    <?php else: ?>
      <div class="small-8 columns"><a href="javascript:;" class="disabled right tiny success button">Sold Out</a></div>
    <?php endif; ?>
  </div>
</div>
<?php endwhile; ?>
</div>
<?php include "footer.php" ?>
