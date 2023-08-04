<?php
  session_start();
  unset($_SESSION['clientmsaid']);
  unset($_SESSION['login']);

  header('location:/');
?>