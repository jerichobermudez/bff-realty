<?php
  session_start();
  if (isset($_SESSION['agentmsaid'])) header('location:/agent');
  if (isset($_SESSION['clientmsaid'])) header('location:/dashboard');
?>
<!DOCTYPE html>
<html>
<?php
  $pageTitle = 'Login Page';
  require_once('includes/header.php');
?>
<body class="login-page">
  <div class="login-box">
    <div class="login-logo">
      <img src="/assets/images/login.png" width="250">
    </div>
    <div class="login-box-body">
      <p class="login-box-msg">Sign in now!</p>
      <form id="loginForm" onsubmit="handleLogin(event)">
        <div class="form-group has-feedback">
          <input type="text" name="username" id="username" class="form-control" placeholder="Username/Email">
          <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
          <input type="password" name="password" id="password" class="form-control" placeholder="Password">
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
          <button type="submit" class="btn btn-primary btn-flat btn-block mt-3" onclick="handleLogin(event)">Sign In</button>
      </form>
    </div>
  </div>
  <?php require_once('includes/footer.php'); ?>
  <script src="/assets/pages/login.js?ver=<?= $version ?>"></script>
</html>