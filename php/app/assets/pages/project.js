let table = $('#projectsTable').DataTable({
  'processing': true,
  'serverSide': true,
  'sortable': true,
  "responsive": true,
  "order" : [],
  ajax: {
    url: '/api/getProjects',
    type: 'POST',
  },
  'displayLength': 20,
  'lengthMenu': [
    [10, 20, 50, 100],
    [10, 20, 50, 100]
  ],
  'columnDefs': [
    {
      'targets': [4],
      'orderable': false,
    }
  ],
  scrollCollapse: false,
  scrollX: 500,
  scrollY: false
});

const handleAddProject = (e) => {
  e.preventDefault();

  let form = $('#addProjectForm');
  $.ajax({
    type:'POST',
    url:'/api/addProject',
    data: form.serialize() + `&addProject`,
    beforeSend: (response) => {
      form.find('input').removeClass('is-invalid');
      form.find('input, button').prop('disabled', true);
    },
    success: (response) => {
      let icon = 'error';
      let message = '';

      if (response.status === 201) {
        icon = 'success';
        setTimeout(() => {
          form[0].reset();
          form.find('input, button').prop('disabled', false);
          table.search('').order([]).page.len(20).draw();
        }, 1000);
      } else if (response.status === 400) {
        form.find('input, button').prop('disabled', false);
        $.each(response.data, (key, value) => {
          form.find(`#${key}`).addClass('is-invalid');
          message += `<br>${key.replace(/_/g, ' ').toLowerCase().replace(/(?: |\b)(\w)/g, (key) => {
            return key.toUpperCase();    
          })} ${value}`;
        });

        message = message.replace(/This /g, '');
      }

      getToast('', response.status, response.message + message, 5, icon);
    }
  });
}

const handleGetEditProject = (id) => {
  let form = $('#editProjectForm');
  $.ajax({
    type:'GET',
    url:'/api/editProject',
    data: { project_id: id },
    beforeSend: (response) => {
      form.find('input').removeClass('is-invalid');
    },
    success: (response) => {
      if (response.status === 200) {
        $.each(response.data, (key, value) => {
          if (key == 'code') {
            form.find(`#edit_property_${key}`).val(value);
          } else {
            form.find(`#edit_project_${key}`).val(value);
          }
        })
        $('#editProjectModal').modal('show');
      } else {
        getToast('', response.status, response.message, 5, 'error');
      }
    }
  });
}

const handleEditProject = (e) => {
  e.preventDefault();

  let form = $('#editProjectForm');
  let modal = $('#editProjectModal');
  $.ajax({
    type: 'POST',
    url: '/api/editProject',
    data: form.serialize() + `&editProject`,
    beforeSend: (response) => {
      form.find('input').removeClass('is-invalid');
      modal.find('.modal-content').prepend(
        $(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`)
      );
    },
    success: (response) => {
      let icon = 'error';
      let message = '';

      if (response.status === 200) {
        icon = 'success';
        message = '<br>Successfully Updated!';
        setTimeout(() => {
          form[0].reset();
          form.find('input, button').prop('disabled', false);
          table.search('').order([]).page.len(20).draw();
          modal.modal('hide');
          modal.find('.overlay').fadeOut();
        }, 700);
      } else if (response.status === 400) {
        form.find('input, button').prop('disabled', false);
        $.each(response.data, (key, value) => {
          form.find(`#edit_${key}`).addClass('is-invalid');
          message += `<br>${key.replace(/_/g, ' ').toLowerCase().replace(/(?: |\b)(\w)/g, (key) => {
            return key.toUpperCase();    
          })} ${value}`;
        });

        message = message.replace(/This /g, '');
        modal.find('.overlay').fadeOut();
      } else {
        modal.find('.overlay').fadeOut();
      }
      getToast('', response.status, response.message + message, 5, icon);
    }
  });
}