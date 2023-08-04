let table = $('#usersTable').DataTable({
  'processing': true,
  'serverSide': true,
  'sortable': true,
  "responsive": true,
  "order" : [],
  ajax: {
    url: '/api/getUsers',
    type: 'POST',
  },
  'displayLength': 20,
  'lengthMenu': [
    [10, 20, 50, 100],
    [10, 20, 50, 100]
  ],
  'columnDefs': [
    {
      'targets': [5],
      'orderable': false,
    }
  ],
  scrollCollapse: false,
  scrollX: 500,
  scrollY: false
});

const handleAddUser = (e) => {
  e.preventDefault();

  let form = $('#addUserForm');
  $.ajax({
    type:'POST',
    url:'/api/addUser',
    data: form.serialize() + `&addUser`,
    beforeSend: (response) => {
      form.find('.form-group').removeClass('has-error');
      form.find('input, select, button').prop('disabled', true);
    },
    success: (response) => {
      let icon = 'error';
      let message = '';

      if (response.status === 201) {
        icon = 'success';
        setTimeout(() => {
          form[0].reset();
          form.find('input, select, button').prop('disabled', false);
          table.search('').order([]).page.len(20).draw();
        }, 1000);
      } else if (response.status === 400) {
        form.find('input, select, button').prop('disabled', false);
        $.each(response.data, (key, value) => {
          form.find(`#${key}`).parent().addClass('has-error');
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

const handleGetEditUser = (id) => {
  let form = $('#editUserForm');
  $.ajax({
    type:'GET',
    url:'/api/editUser',
    data: { user_id: id },
    beforeSend: (response) => {
      form.find('.form-group').removeClass('has-error');
    },
    success: (response) => {
      if (response.status === 200) {
        $.each(response.data, (key, value) => {
          form.find(`#edit_${key}`).val(value).trigger('change');
        })
        $('#editUserModal').modal('show');
      } else {
        getToast('', response.status, response.message, 5, 'error');
      }
    }
  });
}

const handleEditUser = (e) => {
  e.preventDefault();

  let form = $('#editUserForm');
  let modal = $('#editUserModal');
  $.ajax({
    type: 'POST',
    url: '/api/editUser',
    data: form.serialize() + `&editUser`,
    beforeSend: (response) => {
      form.find('.form-group').removeClass('has-error');
      modal.find('.modal-content').append(
        $(`<div class="modal-overlay"><i class="fa fa-refresh fa-spin"></i></div>`)
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
          modal.find('.modal-overlay').fadeOut();
        }, 700);
      } else if (response.status === 400) {
        form.find('input, button').prop('disabled', false);
        $.each(response.data, (key, value) => {
          form.find(`#${key}`).parent().addClass('has-error');
          message += `<br>${key.replace(/_/g, ' ').toLowerCase().replace(/(?: |\b)(\w)/g, (key) => {
            return key.toUpperCase();    
          })} ${value}`;
        });

        message = message.replace(/This /g, '').replace(/Edit /g, '');
        modal.find('.modal-overlay').fadeOut();
      } else {
        modal.find('.modal-overlay').fadeOut();
      }
      getToast('', response.status, response.message + message, 5, icon);
    }
  });
}

const handleDeactivateUser = (id, status) => {
  if (!id) return;

  let message = status == 1 ? 'deactivate' : 'reactivate';
  message = 'Are you sure you want to ' + message + ' the account with ID: ' + id + '?';
  if (confirm(message)) {
    let data = { user_id: id, status: status };
    $.ajax({
      type: 'POST',
      url: '/api/activateDeactivateUser',
      data: $.param(data) + `&activateDeactivate`,
      success: (response) => {
        let icon = 'error';
        let message = '';

        if (response.status === 200) {
          icon = 'success';
          message = '<br>Successfully Updated!';
          table.search('').order([]).page.len(20).draw();
        }

        getToast('', response.status, response.message + message, 5, icon);
      }
    });
  }
}