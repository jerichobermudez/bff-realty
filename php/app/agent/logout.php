<?php
  session_start();
  unset($_SESSION['agent']);
  unset($_SESSION['agent_id']);
  unset($_SESSION['agent_name']);
  unset($_SESSION['voucher_id']);
  unset($_SESSION['voucher_code']);

  header('location:/agent');
?>