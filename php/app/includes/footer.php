  <?php if (isset($_SESSION['clientmsaid'])) { ?>
    <footer class="main-footer text-center">
      <strong>Client Management System &copy; 2023</strong>
    </footer>
    <aside class="control-sidebar control-sidebar-dark"></aside>
  </div>
  <?php } ?>
</body>
<script src="/assets/js/jquery.min.js?ver=<?= $version ?>"></script>
<script src="/assets/js/jquery-ui.min.js?ver=<?= $version ?>"></script>
<script src="/assets/js/bootstrap.min.js?ver=<?= $version ?>"></script>
<script src="/assets/js/adminlte.min.js?ver=<?= $version ?>"></script>
<script src="/assets/js/jquery.toast.min.js?ver=<?= $version ?>"></script>
<?php if (isset($_SESSION['clientmsaid'])) { ?>
  <script src="/assets/js/jquery.dataTables.min.js?ver=<?= $version ?>"></script>
  <script src="/assets/js/moment.min.js?ver=<?= $version ?>"></script>
  <script src="/assets/js/select2.full.min.js?ver=<?= $version ?>"></script>
  <script src="/assets/js/bootstrap-datepicker.min.js?ver=<?= $version ?>"></script>
  <script src="/assets/pages/common.js?ver=<?= $version ?>"></script>
<?php } ?>
