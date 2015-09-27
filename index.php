<?php include "header.php" ?>
<?php
if(!$current_user)
{
  $_SESSION["notice"] = "Please login to continue";
  header("Location: login.php");
  die();
}
?>
<div class="row">
<?php
$result = mysqli_query($con, "SELECT * FROM products");
while($product = mysqli_fetch_assoc($result)):
?>
<div class="small-12 medium-6 large-4 columns">
  <img src="http://placehold.it/500x500" width="100%">
  <div class="row">
    <div class="small-4 columns"><strong><?php echo $product["name"]; ?></strong><br><i>£<?php echo $product["price"]; ?></i></div>
    <?php if($product["stock"] && $current_user): ?>
      <form action="cart.php" method="post">
        <div class="small-3 columns"><input name="number" type="number" value="1" min="1" max="<?php echo $product["stock"]; ?>"></div>
        <div class="small-5 columns"><button type="submit" class="right tiny success button"><i class="fi-shopping-cart"></i> Add to Cart</button></div>
      </form>
    <?php elseif(!$current_user): ?>
      <div class="small-8 columns"><a href="javascript:;" class="disabled right tiny success button">Please Login</a></div>
    <?php else: ?>
      <div class="small-8 columns"><a href="javascript:;" class="disabled right tiny success button">Sold Out</a></div>
    <?php endif; ?>
  </div>
</div>
<?php endwhile; ?>
</div>
<?php include "footer.php" ?>