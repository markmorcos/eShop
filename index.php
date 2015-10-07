<?php include "header.php" ?>
<script type="text/javascript">
$(document).ready(function() {
  $("[type=number]").keypress(function (e) {
      e.preventDefault();
  });
});
</script>
<script>
    $(document).on('click','.buy',function() {
        var i = $(this).attr("i");
        var number = "#number" + i;
        var quantity = Number($(number).val());
        var price = "#price" + i;
        price = Number($(price).text());
        var total = quantity * price;
        var quantityModal = "#quantityModal"+ i;
        $(quantityModal).text(quantity);
        var maxi = Number($(number + "1").attr('max'));
        if(quantity <= maxi) $(number + "1").val(quantity);
        else $(number + "1").val("0");
        var totalModal ="#totalModal" + i;
        $(totalModal).text(total);
});
</script>
<?php
if(isset($_POST["cart"]))
{
    $product_id = $_POST["product_id"];
    $product_num = $_POST["number"];
    if($product_num <= 0)
    {
		$_SESSION["alert"] = "Either you have chosen to buy zero items or your current cart equals all the stock";
		header("Location: index.php");
		die();
    }
    $stock = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM products WHERE id = $product_id"));
    $result = mysqli_query($con, "SELECT * FROM cart WHERE user_id = $id AND product_id = $product_id");
    $exists = mysqli_fetch_assoc($result);
    if($exists)
    {
      if($exists["quantity"] + $product_num <= $stock["stock"])
      {
        mysqli_query($con, "UPDATE cart SET quantity = quantity + $product_num WHERE user_id = $id AND product_id = $product_id");
      }
    }
    else mysqli_query($con, "INSERT INTO cart VALUES(NULL, $id, $product_id, $product_num)");
    $_SESSION["notice"] = "Item added to cart successfully";
    header("Location: index.php");
    die();
}
?>
<div class="row" data-equalizer>
    <h1>Products</h1>
    <?php
    $result = mysqli_query($con, "SELECT * FROM products");
    $i = 1;
    while($product = mysqli_fetch_assoc($result)):
        $product_id = $product["id"];
        $cart = mysqli_query($con, "SELECT * FROM cart WHERE user_id = $id AND product_id = $product_id;");
        $amount = mysqli_fetch_assoc($cart);
        ?>
        <div class="small-12 medium-6 large-4 columns">
            <div data-equalizer-watch>
                <img src="<?= $product["image"] ? $uploads["files"] . "products/" . $product["image"] : "img/placeholder.png"; ?>" width="100%">
            </div>
            <div class="row">
                <div class="small-4 columns"><strong><?php echo $product["name"]; ?></strong><br><i>$<span id="price<?php echo $i; ?>"><?php echo $product["price"]; ?></span></i></div>
                <?php if($product["stock"] && $current_user): ?>
                        <div id="product<?php echo $i ?>" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
                            <h4 id="modalTitle">Invoice</h4>
                            <div class="row">
                                <strong>
                                    <div class="small-3 columns">Name</div>
                                    <div class="small-3 columns">Quantity</div>
                                    <div class="small-3 columns">Price Per Item</div>
                                    <div class="small-3 columns">Total</div>
                                </strong>
                            </div>
                            <div class="row">
                                <div class="small-3 columns"><?php echo $product["name"]; ?></div>
                                <div class="small-3 columns" name="quantity"><span id= "quantityModal<?php echo $i ?>"></span></div>
                                <div class="small-3 columns"><?php echo $product["price"]; ?></div>
                                <div class="small-3 columns" name="total"><span id="totalModal<?php echo $i ?>"></span></div>
                            </div>
                            <hr>
                            <form method="post">
                                <input type="hidden" name="cart" value="true">
                                <input type="hidden" name="product_id" value="<?= $product["id"]; ?>">
                                <input type="hidden" id="number<?php echo $i ?>1" name="number" value="<?php echo $product["stock"] - $amount["quantity"] > 0 ? 1 : 0; ?>" min="<?php echo $product["stock"] - $amount["quantity"] > 0 ? 1 : 0; ?>" max="<?php echo $product["stock"] - $amount["quantity"]; ?>">
                                <button type="submit" class="right tiny success button" style="margin-top:5px;"><i class="fi-shopping-cart"></i> Add to Cart</button>
                            </form>
                                <a class="close-reveal-modal" aria-label="Close">&#215;</a>
                        </div>
                        <input type="hidden" name="cart" value="true">
                        <input type="hidden" name="product_id" value="<?= $product["id"]; ?>">
                        <div class="small-3 columns"><input id="number<?php echo $i ?>" type="number" value="<?php echo $product["stock"] - $amount["quantity"] > 0 ? 1 : 0; ?>" min="<?php echo $product["stock"] - $amount["quantity"] > 0 ? 1 : 0; ?>" max="<?php echo $product["stock"] - $amount["quantity"]; ?>"></div>
                        <div class="small-5 columns"><a href="#" class="right tiny success button buy" data-reveal-id="product<?php echo $i; ?>" i="<?php echo $i; ?>" style="margin-top:5px;"><i class="fi-shopping-cart"></i> Add to Cart</a></div>
                <?php elseif(!$current_user): ?>
                    <div class="small-8 columns"><a href="<?= $apath; ?>login.php" class="right tiny success button" data-reveal-id="product" style="margin-top:5px;">Login to buy</a></div>
                <?php else: ?>
                    <div class="small-8 columns"><a href="javascript:;" class="disabled right tiny alert button" style="margin-top:5px;">Sold Out</a></div>
                <?php endif; ?>
            </div>
        </div>

    <?php $i++;endwhile; ?>
</div>
<?php include "footer.php" ?>
