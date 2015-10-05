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
@mysqli_free_result($result2);
mysqli_close($con);
?>
