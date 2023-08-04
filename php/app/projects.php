<?php
  session_start();
  $pageTitle = 'Projects';
  require_once('includes/session.php');
?>

<!DOCTYPE html>
<html>
<?php require_once('includes/header.php'); ?>
<?php require_once('includes/sidebar.php'); ?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Projects
      <small></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-home"></i> Projects</a></li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-4">
        <div class="box rounded-0">
          <div class="box-header with-border">
            <h3 class="box-title">Add Project</h3>
          </div> 
          <div class="box-body pt-1">
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
        <div class="box rounded-0">
          <div class="box-header with-border">
            <h3 class="box-title">Manage Projects</h3>
          </div>
          <div class="box-body text-sm">
            <div class="table-responsive no-border">
              <table class="table table-hover nav-legacy" id="projectsTable" width="100%">
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
  </section>
</div>
<div class="modal fade" id="editProjectModal" tabindex="-1" role="dialog" aria-labelledby="editProjectModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header p-3">
        <label class="h4 modal-title" id="editProjectModalTitle">Edit Project</label>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="fa fa-times-circle fa-sm"></span>
        </button>
      </div>
      <form id="editProjectForm" onsubmit="handleEditProject(event)">
        <div class="modal-body pb-1">
          <div class="box box-solid rounded-0">
            <div class="box-body">
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
          </div>
        </div>
        <div class="modal-footer p-2">
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