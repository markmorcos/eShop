<?php
include "includes/functions.php";
include "includes/engine.php";
$url = $path . $page["name"];
$current = explode("?", $page["name"])[0];
$login = $_SESSION["admin_id"];
if($login)
{
  $current_admin = $db->querySelectSingle("SELECT * FROM admins WHERE id = '$login'");
  $current_store = $db->querySelectSingle("SELECT * FROM stores WHERE id = '" . $current_admin['store_id'] . "'");
}
//if($page["name"] != "login.php" && !$login) header("Location: {$path}login.php");
//elseif($page["name"] == "login.php" && $login) header("Location: {$path}index.php");
$id = $_GET["id"] ? $_GET["id"] : $_POST["id"];
$action = $_GET["action"] ? $_GET["action"] : $_POST["action"];
$submit = $_GET["submit"] ? $_GET["submit"] : $_POST["submit"];
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $page["title"] . ' - ' . $title; ?></title>
<link href="<?php echo $path; ?>css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo $path; ?>css/bootstrap-theme.min.css" rel="stylesheet">
<link href="<?php echo $path; ?>css/theme.css" rel="stylesheet">
<link href="<?php echo $path; ?>css/font-awesome.css" rel="stylesheet">
<link href="<?php echo $path; ?>css/index.css" rel="stylesheet">
<link href="<?php echo $path; ?>css/prettify.css" rel="stylesheet">
<link href="<?php echo $path; ?>css/jquery-ui.min.css" rel="stylesheet">
<link href="<?php echo $path; ?>css/jquery-ui.structure.min.css" rel="stylesheet">
<link href="<?php echo $path; ?>css/jquery-ui.theme.min.css" rel="stylesheet">
<link href="<?php echo $path; ?>css/style.css" rel="stylesheet">
<link rel="shortcut icon" href="<?php echo $apath; ?>images/favicon.png" type="image/x-icon">
<script src="<?php echo $path; ?>js/jquery-latest.min.js"></script>
<script src="<?php echo $path; ?>js/bootstrap-wysiwyg.js"></script>
<script src="<?php echo $path; ?>js/prettify.js"></script>
<script src="<?php echo $path; ?>js/jquery-ui.min.js"></script>
<script>
$(function() {
$(".editor").wysiwyg();
$(".datepicker").datepicker({
dateFormat: "yy-mm-dd",
showButtonPanel: true
});
$(".editor").focusout(function() {
$(this).parent().find("textarea[name=" + $(this).attr("for") + "]").html($(this).html());
});
});
</script>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDSd9Rv-xBPz5441EgwZuY8CvqJgXq7cCM"></script>
<script type="text/javascript">
function initialize() {
var mapOptions = {
center: { lat: Number(document.getElementById("latitude").value), lng: Number(document.getElementById("longitude").value) },
zoom: Number(document.getElementById("zoom").value)
};
var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
google.maps.event.addListener(map, 'center_changed', function() {
document.getElementById("latitude").value = map.center.A;
document.getElementById("longitude").value = map.center.F;
document.getElementById("zoom").value = map.zoom;
});
}
google.maps.event.addDomListener(window, 'load', initialize);
</script>
</head>
<body>
<div class="navbar navbar-fixed-top">
<div class="container">
<div class="navbar-header">
<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
<span class="sr-only">Toggle navigation</span>
<span class="navbar-inverse icon-bar"></span>
<span class="navbar-inverse icon-bar"></span>
<span class="navbar-inverse icon-bar"></span>
</button>
</div>
<div id="navbar" class="navbar-collapse collapse">
<ul class="nav navbar-nav">
<li><a><?php echo $title ?></a></li>
<li <?php if($current == "products.php") echo 'class="active"'; ?>><a href="<?php echo $path; ?>products.php">Products</a></li>
</ul>
</div>
</div>
</div>
