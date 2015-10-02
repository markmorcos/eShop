<div class="container">
<hr>
<footer>
<p class="navbar-left">Copyright &copy; 2015 <?php echo $title; ?>. All rights reserved.</p>
<?php if($login) { ?>
<div class="navbar-right"><a href="<?php echo $path; ?>logout.php">Logout (<?php echo $current_admin["username"]; ?>)</a></div>
<?php } ?>
</footer>
</div>
<script src="<?php echo $path; ?>js/bootstrap.min.js"></script>
<script src="<?php echo $path; ?>js/jquery.hotkeys.js"></script>
<script src="<?php echo $path; ?>js/holder.js"></script>
</body>
</html>
