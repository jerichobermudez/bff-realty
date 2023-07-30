<?php
  session_start();
  $pageTitle = 'Users';
  require_once('includes/session.php');
  if ((int) $role !== (int) UserRole::ADMIN) { header('Location: /'); }
?>

<!DOCTYPE html>
<html>
<?php require_once('includes/header.php'); ?>
<body class="layout-navbar-fixed layout-fixed sidebar-mini">
  <div class="wrapper">
    <?php require_once('includes/sidebar.php'); ?>
    <div class="content-wrapper">
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Users</h1>
            </div>
          </div>
        </div>
      </section>

      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-4">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Add User</h3>
                </div> 
                <div class="card-body pt-1">
                  <form id="addUserForm" onsubmit="handleAddUser(event)">
                    <div class="form-group mb-1">
                      <label for="name" class="text-sm">Name:</label>
                      <input type="text" class="form-control form-control-sm mt-n2" name="name" id="name" placeholder="Name" autocomplete="off">
                    </div>
                    <div class="form-group mb-1">
                      <label for="username" class="text-sm">Username:</label>
                      <input type="text" class="form-control form-control-sm mt-n2" name="username" id="username" placeholder="Username" autocomplete="off">
                    </div>
                    <div class="form-group mb-1">
                      <label for="email" class="text-sm">Email:</label>
                      <input type="text" class="form-control form-control-sm mt-n2" name="email" id="email" placeholder="Email" autocomplete="off">
                    </div>
                    <div class="form-group mb-1">
                      <label for="phone" class="text-sm">Contact No.:</label>
                      <input type="text" class="form-control form-control-sm mt-n2" name="phone" id="phone" placeholder="Contact No." autocomplete="off">
                    </div>
                    <div class="form-group mb-1">
                      <label for="role" class="text-sm">Role:</label>
                      <select class="form-control form-control-sm mt-n2" name="role" id="role">
                        <option value>Choose Role</option>
                        <?php
                          foreach ($userRole as $key => $value) {
                            echo '<option value="' . $value . '">' .
                              UserRole::getTextValue($value) .
                            '</option>';
                          }
                        ?>
                      </select>
                    </div>
                    <div class="form-group mb-1">
                      <label for="password" class="text-sm">Password:</label>
                      <input type="password" class="form-control form-control-sm mt-n2" name="password" id="password" placeholder="Password" autocomplete="off">
                    </div>
                    <div class="form-group mb-3">
                      <label for="confirm_password" class="text-sm">Confirm Password:</label>
                      <input type="password" class="form-control form-control-sm mt-n2" name="confirm_password" id="confirm_password" placeholder="Confirm Password" autocomplete="off">
                    </div>
                    <button type="submit" class="btn btn-primary btn-flat btn-block btn-sm">Save</button>
                  </form>
                </div>
              </div>
            </div>
            <div class="col-md-8">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Manage Users</h3>
                </div>
                <div class="card-body text-sm">
                  <div class="table-responsive">
                    <table class="table table-hover nav-legacy" id="usersTable" width="100%">
                      <thead>
                        <tr>
                          <th style="min-width: 1px;">ID</th>
                          <th style="min-width: 90px;">Name</th>
                          <th>Username</th>
                          <th>Email</th>
                          <th>Role</th>
                          <th class="text-center" style="min-width: 100px;">Setting</th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
    <div class="modal fade" id="editUserModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="editUserModalTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header px-3 py-2">
            <h5 class="modal-title" id="editUserModalTitle">Edit User</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" class="fa fa-times-circle"></span>
            </button>
          </div>
          <form id="editUserForm" onsubmit="handleEditUser(event)">
            <div class="modal-body">
              <input type="hidden" id="edit_id" name="user_id">
              <div class="form-group mb-1">
                <label for="edit_name" class="text-sm">Name:</label>
                <input type="text" class="form-control form-control-sm mt-n2" name="edit_name" id="edit_name" placeholder="Name">
              </div>
              <div class="form-group mb-1">
                <label for="edit_username" class="text-sm">Username:</label>
                <input type="text" class="form-control form-control-sm mt-n2" name="edit_username" id="edit_username" placeholder="Username">
              </div>
              <div class="form-group mb-1">
                <label for="edit_email" class="text-sm">Email:</label>
                <input type="text" class="form-control form-control-sm mt-n2" name="edit_email" id="edit_email" placeholder="Email">
              </div>
              <div class="form-group mb-1">
                <label for="edit_phone" class="text-sm">Contact No.:</label>
                <input type="text" class="form-control form-control-sm mt-n2" name="edit_phone" id="edit_phone" placeholder="Contact No.">
              </div>
              <div class="form-group mb-1">
                <label for="edit_role" class="text-sm">Role:</label>
                <select class="form-control form-control-sm mt-n2" name="edit_role" id="edit_role">
                  <option value>Choose Role</option>
                  <?php
                    foreach ($userRole as $key => $value) {
                      echo '<option value="' . $value . '">' .
                        UserRole::getTextValue($value) .
                      '</option>';
                    }
                  ?>
                </select>
              </div>
              <div class="form-group mb-1">
                <label for="edit_password" class="text-sm">Password:</label>
                <input type="password" class="form-control form-control-sm mt-n2" name="edit_password" id="edit_password" placeholder="Password">
              </div>
              <div class="form-group mb-3">
                <label for="edit_confirm_password" class="text-sm">Confirm Password:</label>
                <input type="password" class="form-control form-control-sm mt-n2" name="edit_confirm_password" id="edit_confirm_password" placeholder="Confirm Password">
              </div>
            </div>
            <div class="modal-footer px-2 py-1">
              <button type="button" class="btn btn-secondary btn-flat btn-sm" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary btn-flat btn-sm">Update</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  <?php require_once('includes/footer.php') ?>
  <script src="/assets/pages/user.js?ver=<?= $version ?>"></script>
</html>