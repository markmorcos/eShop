  <script src="<?= $apath; ?>js/vendor/jquery.js"></script>
  <script src="<?= $apath; ?>js/foundation.min.js"></script>
  <script>
    $(document).foundation();
  </script>
  </body>
</html>
<?php
unset($_SESSION["alert"]);
unset($_SESSION["notice"]);
unset($_SESSION["login_alert"]);
unset($_SESSION["login_notice"]);
mysqli_free_result($result);
mysqli_close($con);
?>
