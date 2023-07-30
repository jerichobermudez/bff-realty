<?php
  session_start();
  $pageTitle = 'Projects';
  require_once('includes/session.php');
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
              <h1>Projects</h1>
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
                  <h3 class="card-title">Add Project</h3>
                </div> 
                <div class="card-body pt-1">
                  <form id="addProjectForm" onsubmit="handleAddProject(event)">
                    <div class="form-group mb-1">
                      <label for="project_name" class="text-sm">Name:</label>
                      <input type="text" class="form-control form-control-sm mt-n2" name="project_name" id="project_name" placeholder="Project Name">
                    </div>
                    <div class="form-group mb-1">
                      <label for="project_location" class="text-sm">Location:</label>
                      <input type="text" class="form-control form-control-sm mt-n2" name="project_location" id="project_location" placeholder="Project Location">
                    </div>
                    <div class="form-group mb-3">
                      <label for="property_code" class="text-sm">Code:</label>
                      <input type="text" class="form-control form-control-sm mt-n2" name="property_code" id="property_code" placeholder="Property Code">
                    </div>
                    <button type="submit" class="btn btn-primary btn-flat btn-block btn-sm">Save</button>
                  </form>
                </div>
              </div>
            </div>
            <div class="col-md-8">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Manage Projects</h3>
                </div>
                <div class="card-body text-sm">
                  <div class="table-responsive">
                    <table class="table border-3 table-hover nav-legacy" id="projectsTable" width="100%">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Name</th>
                          <th>Location</th>
                          <th>Code</th>
                          <th class="text-center">Setting</th>
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
    <div class="modal fade" id="editProjectModal" tabindex="-1" role="dialog" aria-labelledby="editProjectModalTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header px-3 py-2">
            <h5 class="modal-title" id="editProjectModalTitle">Edit Project</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" class="fa fa-times-circle"></span>
            </button>
          </div>
          <form id="editProjectForm" onsubmit="handleEditProject(event)">
            <div class="modal-body">
              <input type="hidden" id="edit_project_id" name="project_id">
              <div class="form-group mb-1">
                <label for="edit_project_name" class="text-sm">Name:</label>
                <input type="text" class="form-control form-control-sm mt-n2" name="project_name" id="edit_project_name" placeholder="Project Name">
              </div>
              <div class="form-group mb-1">
                <label for="edit_project_location" class="text-sm">Location:</label>
                <input type="text" class="form-control form-control-sm mt-n2" name="project_location" id="edit_project_location" placeholder="Project Location">
              </div>
              <div class="form-group mb-1">
                <label for="edit_project_code" class="text-sm">Code:</label>
                <input type="text" class="form-control form-control-sm mt-n2" name="property_code" id="edit_property_code" placeholder="Property Code">
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
  <script src="/assets/pages/project.js?ver=<?= $version ?>"></script>
</html>