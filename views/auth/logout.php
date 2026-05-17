<?php
session_start();

session_unset();
session_destroy();

/*
  BACK TO MAIN INDEX (ROOT)
*/
header("Location: ../../index.php");
exit;
?>