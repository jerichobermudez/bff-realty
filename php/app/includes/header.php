<?php $version = '1.0.0'; ?>
<head>
  <meta charset='utf-8'>
  <meta http-equiv='X-UA-Compatible' content='IE=edge'>
  <title><?= $pageTitle ? 'CMS | '. $pageTitle : 'CMS' ?></title>
  <meta name='viewport' content='width=device-width, initial-scale=1'>
  <link rel="icon" href="/assets/images/logo.png">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback?ver=<?= $version ?>">
  <link rel="stylesheet" href="/assets/css/bootstrap.min.css?ver=<?= $version ?>">
  <link rel="stylesheet" href="/assets/css/font-awesome.all.min.css?ver=<?= $version ?>">
  <?php if (isset($_SESSION['clientmsaid'])) { ?>
    <link rel="stylesheet" href="/assets/css/jquery.dataTables.min.css?ver=<?= $version ?>">
    <link rel="stylesheet" href="/assets/css/select2.min.css?ver=<?= $version ?>">
    <link rel="stylesheet" href="/assets/css/bootstrap-datepicker.min.css?ver=<?= $version ?>">
  <?php } ?>
  <link rel="stylesheet" href="/assets/css/AdminLTE.min.css?ver=<?= $version ?>">
  <link rel="stylesheet" href="/assets/css/skins/_all-skins.min.css?ver=<?= $version ?>">
  <link rel="stylesheet" href="/assets/css/jquery.toast.min.css?ver=<?= $version ?>">
  <link rel="stylesheet" href="/assets/css/margin-padding.css?ver=<?= $version ?>">
  <link rel="stylesheet" href="/assets/css/custom.css?ver=<?= $version ?>">
</head>
