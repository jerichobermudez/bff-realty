<?php
  session_start();
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
    <div class="login-logo mt-n5 mb-0">
      <img src="/assets/images/login.png" width="300" class="mt-1 mb-4">
      <p class="h2 my-3"></p>
    </div>
    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg h5 text-dark">Sign in now!</p>
        <form id="loginForm" onsubmit="handleLogin(event)">
          <div class="input-group mb-3">
            <input type="text" name="username" id="username" class="form-control rounded-0" placeholder="Username">
            <div class="input-group-append">
              <div class="input-group-text rounded-0">
                <span class="fas fa-user"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" name="password" id="password" class="form-control rounded-0" placeholder="Password">
            <div class="input-group-append">
              <div class="input-group-text rounded-0">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <button type="submit" class="btn bg-gradient-primary btn-flat btn-block mt-3" onclick="handleLogin(event)">Sign In</button>
        </form>
      </div>
    </div>
  </div>
  <?php require_once('includes/footer.php'); ?>
  <script src="/assets/pages/login.js?ver=<?= $version ?>"></script>
</html>