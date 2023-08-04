let table = $('#clientsTable').DataTable({
  'processing': true,
  'serverSide': true,
  'sortable': true,
  "responsive": true,
  "order" : [],
  ajax: {
    url: '/api/getClients',
    type: 'POST',
  },
  'displayLength': 20,
  'lengthMenu': [
    [10, 20, 50, 100],
    [10, 20, 50, 100]
  ],
  'columnDefs': [
    {
      'targets': [3, 4, 5, 6],
      'orderable': false,
    }
  ],
  scrollCollapse: false,
  scrollX: 500,
  scrollY: false
});

$(document).ready(() => {
  $('#property_lot_area, #property_price_per_sqm, #terms_of_payment, #downpayment_amount').on('change, input', () => { handleComputeMonthlyAmortization(); });

  $('#edit_property_lot_area, #edit_property_price_per_sqm, #edit_terms_of_payment, #edit_downpayment_amount').on('change, input', () => { handleComputeMonthlyAmortization('#edit_monthly_amortization', '#edit_property_lot_area', '#edit_property_price_per_sqm', '#edit_terms_of_payment', '#edit_downpayment_amount'); });
});

const handleGetEditClient = (id) => {
  if (!id) return;

  let form = $('#editClientForm');
  let modal = $('#editClientModal');
  modal.modal('show');
  form.find('#edit_client_id').val(id);

  $.ajax({
    type:'GET',
    url:'/api/getEditClientDetails',
    data: { client_id: id },
    beforeSend: () => {
      modal.find('.modal-body').animate({
        scrollTop: modal.find('.modal-body').offset().top - 220
      }, 300);
      form[0].reset();
      form.find('.select2-selection').removeClass('select-has-error');
      form.find('.form-group').removeClass('has-error');
      form.find('.select2-get-edit-properties').val(null).trigger('change');
      modal.find('.modal-content').prepend(
        $(`<div class="modal-overlay"><i class="fa fa-refresh fa-spin"></i></div>`)
      );
    },
    success: (response) => {
      if (response.status === 200) {
        if (response.data) {
          let data = response.data;
          form.find('#edit_customer_no').val(data.customer_no ?? '');
          form.find('#edit_firstname').val(data.client_firstname ?? '');
          form.find('#edit_middlename').val(data.client_middlename ?? '');
          form.find('#edit_lastname').val(data.client_lastname ?? '');
          form.find('#edit_address').val(data.client_address ?? '');
          form.find('#edit_contact').val(data.client_contact ?? '');
          form.find('#edit_email').val(data.client_email ?? '');
          form.find('#edit_birthday').val(data.client_birthday ?? '');
          form.find('#edit_marital_status').val(data.marital_status ?? null).trigger('change');
          form.find('#edit_spouse_name').val(data.spouse_name ?? '');
          form.find('#edit_spouse_contact').val(data.spouse_contact ?? '');
          form.find('#edit_spouse_email').val(data.spouse_email ?? '');
          form.find('#edit_company_name').val(data.company_name ?? '');
          form.find('#edit_company_address').val(data.company_address ?? '');
          form.find('#edit_company_contact').val(data.company_contact ?? '');
          form.find('#edit_year_of_stay').val(data.years_worked ?? '');
          form.find('#edit_position').val(data.position ?? '');
          form.find('#edit_tin_id').val(data.tin_id ?? '');
          form.find('#edit_sss_id').val(data.sss_id ?? '');
          form.find('#edit_monthly_salary').val(data.monthly_salary ?? '');
        }

        handleGetClientPropertySelections('.select2-get-edit-properties', id);
      } else {
        getToast('', response.status, response.message, 5, 'error');
      }
    },
    complete: () => {
      modal.find('.modal-overlay').fadeOut();
    }
  });
}

const handleGetEditProperty = (id) => {
  let form = $('#editClientForm');
  let modal = $('#editClientModal');
  let agentSelections = form.find(".select2-get-project-agents").select2();

  if (!id) {
    form.find('#edit_project_id').val('');
    form.find('#edit_project_location').val('');
    form.find('#edit_property_phase').val('');
    form.find('#edit_property_block').val('');
    form.find('#edit_property_lot').val('');
    form.find('#edit_property_lot_area').val('');
    form.find('#edit_property_price_per_sqm').val('');
    form.find('#edit_monthly_amortization').val('');
    form.find('#edit_terms_of_payment').val(null).trigger('change');
    form.find('#edit_downpayment_amount').val('');
    form.find('#edit_downpayment_date').val('');
    form.find('#edit_downpayment_due_date').val('');
    form.find('#edit_sales_coordinator').val('');
    agentSelections.val(null).trigger("change");
    form.find('#edit_assisting_coordinator').val('');
    form.find('#edit_property_remarks').val('');

    return;
  }

  $.ajax({
    type:'GET',
    url:'/api/getEditPropertyDetails',
    data: { property_id: id },
    beforeSend: (response) => {
      agentSelections.val(null).trigger("change");
      modal.find('.modal-content').append(
        $(`<div class="modal-overlay"><i class="fa fa-refresh fa-spin"></i></div>`)
      );
    },
    success: (response) => {
      if (response.status === 200) {
        if (response.data) {
          let data = response.data;
          let agentId = data.agent_id;

          handleSelectProjectAgent(agentId);
          form.find('#edit_project_id').val(data.project_id ?? '');
          form.find('#edit_project_location').val(data.project_location ?? '');
          form.find('#edit_property_phase').val(data.phase ?? '');
          form.find('#edit_property_block').val(data.block ?? '');
          form.find('#edit_property_lot').val(data.lot ?? '');
          form.find('#edit_property_lot_area').val(data.lot_area ?? '');
          form.find('#edit_property_price_per_sqm').val(data.price_per_sqm ?? '');
          form.find('#edit_monthly_amortization').val(data.monthly_amortization ?? '');
          form.find('#edit_terms_of_payment').val(data.payment_terms ?? null).trigger('change');
          form.find('#edit_downpayment_amount').val(data.downpayment_amount ?? '');
          form.find('#edit_downpayment_date').val(data.downpayment_date ?? '');
          form.find('#edit_downpayment_due_date').val(data.downpayment_due_date ?? '');
          form.find('#edit_sales_coordinator').val(data.sales_coordinator ?? '');
          form.find('#edit_assisting_coordinator').val(data.assistant_coordinator ?? '');
          form.find('#edit_property_remarks').val(data.remarks ?? '');
          agentSelections.val(agentId).trigger("change");
        }
      } else {
        getToast('', response.status, response.message, 5, 'error');
      }
    },
    complete: () => {
      modal.find('.modal-overlay').fadeOut('slow');
    }
  });
}

const handleEditClient = (e) => {
  e.preventDefault();

  let form = $('#editClientForm');
  let modal = $('#editClientModal');
  $.ajax({
    type:'POST',
    url:'/api/editClient',
    data: form.serialize() + `&editClient`,
    beforeSend: (response) => {
      form.find('.select2-selection').removeClass('select-has-error');
      form.find('.form-group').removeClass('has-error');
      form.find('input, textarea, button, select').prop('disabled', true);
      modal.find('.modal-content').append(
        $(`<div class="modal-overlay"><i class="fa fa-refresh fa-spin"></i></div>`)
      );
    },
    success: (response) => {
      let icon = 'error';
      let message = '';

      if (response.status === 200) {
        icon = 'success';
        setTimeout(() => {
          table.search('').order([]).page.len(10).draw();
          modal.modal('hide');
          modal.find('.modal-overlay').fadeOut();
        }, 1000);
      } else if (response.status === 400 || response.status === 409) {
        let fieldError = null;
        $.each(response.data, (key, data) => {
          if (key == 'fields') {
            $.each(data, (index, value) => {
              if (index === 'project_name') {
                form.find('.select2-selection').addClass('select-has-error');
              }
              form.find(`#${index}`).parent().addClass('has-error');
              fieldError = 'Please fill up the required fields.';
            });
          } else if (key === 'exists') {
            if (data.length > 0) {
              form.find('.select2-selection').addClass('select-has-error');
              form.find('#edit_property_id, #edit_property_block, #edit_property_lot').parent().addClass('has-error');
            }
            message = fieldError !== null ? fieldError : data;
          }
        });
        modal.find('.modal-overlay').fadeOut();
      } else {
        modal.find('.modal-overlay').fadeOut();
      }
      form.find('input, select, textarea, button').prop('disabled', false);

      getToast('', response.status, response.message + '<br>' + message, 5, icon);
    }
  });
}

const handleGetClientDetails = (id) => {
  let modal = $('#getClientDetailsModal');

  $.ajax({
    type:'GET',
    url:'/api/getClient',
    data: { client_id: id },
    beforeSend: (response) => {
      modal.modal('show');
      modal.find('.modal-body').html('');
      modal.find('.modal-content').append(
        $(`<div class="modal-overlay"><i class="fa fa-refresh fa-spin"></i></div>`)
      );
    },
    success: (response) => {
      if (response.status === 200) {
        let data = JSON.parse(response.data);
        let htmlLayout = data.html;
        modal.find('.modal-body').html($(htmlLayout));
      } else {
        getToast('', response.status, response.message, 5, 'error');
      }
    },
    complete: () => {
      modal.find('.modal-overlay').fadeOut('slow');
    }
  });
}

const handleViewPaymentModal = (id) => {
  let modal = $('#addPaymentModal');
  let form = $('#addPaymentForm');
  form[0].reset();
  modal.modal('show');
  modal.find('input, select').removeClass('is-invalid');
  modal.find('#payment_client_id').val(id);
  handleGetClientPropertySelections('.select2-get-properties', id);
  handleGetPaymentMethods('.select2-get-payment-methods');
  handleShowPaymentHistory();
}

const handleViewPropertyModal = (id) => {
  let modal = $('#addPropertyModal');
  let form = $('#addPropertyForm');
  form[0].reset();
  modal.modal('show');
  form.find('.select2-project-name').val(null).trigger('change');
  form.find('input, select').removeClass('is-invalid');
  form.find('#property_client_id').val(id);
  handleGetProjectSelections('.select2-project-name', '#property_project_location');
}

const handleViewSOAModal = (id) => {
  let modal = $('#propertiesPaymentSummaryModal');
  modal.modal('show');
  handleGetPropertiesTable(id);
}

const handleShowPaymentHistory = (id = null) => {
  let paymentHistory = $('#paymentHistory');
  let form = $('#addPaymentForm');
  form.find('button[type="submit"]').prop('disabled', false);
  form.find('.select2-selection').removeClass('select-has-error');
  form.find('.form-group').removeClass('has-error');
  if (!id) {
    paymentHistory.html($(`<div class="text-center text-lg font-weight-bold"><label>Please Choose Property!</label></div>`));

    return false;
  }

  $.ajax({
    type:'GET',
    url:'/api/getPaymentHistory',
    data: { property_id: id },
    beforeSend: (response) => {
      paymentHistory.html($('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>'));
    },
    success: (response) => {
      let htmlLayout = '';
      if (response.status === 200) {
        let data = response.data;

        htmlLayout = `
          <div class='d-flex justify-content-start align-items-start' style='gap: 0.3rem;'>
            <div class='dropdown'>
              <button type='button' class='btn btn-flat btn-primary btn-xs dropdown-toggle' data-toggle='dropdown' aria-expanded='false' title='View'>
                <span class='fa fa-eye fa-fw fa-sm'></span>
              </button>
              <div class='dropdown-menu rounded-0 py-0' style='margin-right: 3px; margin-top: -1px;'>
                <form action='/api/getStatementOfAccount' method='POST' target='_blank'>
                  <input type='hidden' name='property_id' value='${id}'>
                  <button class='btn btn-flat btn-link dropdown-item mt-0' name='view'>
                    <span class='fa fa-eye fa-fw fa-sm'></span> Statement Of Account
                  </button>
                </form>

                <form action='/api/getPaymentSummary' method='POST' target='_blank'>
                  <input type='hidden' name='property_id' value='${id}'>
                  <button class='btn btn-flat btn-link dropdown-item mt-0' name='view'>
                    <span class='fa fa-eye fa-fw fa-sm'></span> Payment Summary
                  </button>
                </form>
              </div>
            </div>

            <div class='dropdown'>
              <button type='button' class='btn btn-flat btn-primary btn-xs dropdown-toggle' data-toggle='dropdown' aria-expanded='false' title='Download'>
                <span class='fa fa-download fa-fw fa-sm'></span>
              </button>
              <div class='dropdown-menu rounded-0 py-0' style='margin-right: 3px; margin-top: -1px;'>
              <form action='/api/getStatementOfAccount' method='POST' target='_blank'>
                <input type='hidden' name='property_id' value='${id}'>
                <button class='btn btn-flat btn-link dropdown-item mt-0' name='download'>
                  <span class='fa fa-download fa-fw fa-sm'></span> Statement Of Account
                </button>
              </form>

              <form action='/api/getPaymentSummary' method='POST' target='_blank'>
                <input type='hidden' name='property_id' value='${id}'>
                <button class='btn btn-flat btn-link dropdown-item mt-0' name='download'>
                  <span class='fa fa-download fa-fw fa-sm'></span> Payment Summary
                </button>
              </form>
              </div>
            </div>
          </div>
          <table class="table table-sm table-bordered table-striped mb-2">
            <thead>
              <tr>
                <th>Ref&nbsp;No.</th>
                <th>Payment&nbsp;Type</th>
                <th>Payment&nbsp;Date</th>
                <th>Amount</th>
              </tr>
            </thead>
            <tbody>
        `;

        $.each(data.payments, (key, obj) => {
          htmlLayout += `
              <tr>
                <td>${obj.reference_no}</td>
                <td>${obj.payment_type}</td>
                <td>${obj.payment_date}</td>
                <td align="right"><b>${obj.payment_amount}</b></td>
              </tr>
          `;
        });

        htmlLayout += `
              <tr>
                <td colspan="3" align="right"><b>Total Payment Amount:</b></td>
                <td align="right"><b><u>${data.total}</u></b></td>
              </tr>
              <tr>
                <td colspan="3" align="right"><b>Total Contract Price:</b></td>
                <td align="right"><b><u>${data.total_contract_price}</u></b></td>
              </tr>
              <tr>
                <td colspan="3" align="right"><b>Remaining Balance:</b></td>
                <td align="right"><b id="remainingBalance"><u>${data.remaining_balance}</u></b></td>
              </tr>
            </tbody>
          </table>
        `;
        
        if (data.remaining_balance === '0.00') {
          form.find('button[type="submit"]').prop('disabled', true);
        }
      } else {
        htmlLayout = `<div class="text-center text-lg font-weight-bold"><label>No Payment History Found!</label></div>`;
      }

      paymentHistory.html($(htmlLayout));
      
    },
    complete: () => {
      paymentHistory.find('.overlay').fadeOut(1200);
    }
  });
}

const handleAddPayment = (e) => {
  e.preventDefault();

  let form = $('#addPaymentForm');
  $.ajax({
    type:'POST',
    url:'/api/addPayment',
    data: form.serialize() + `&addPayment`,
    beforeSend: (response) => {
      form.find('.select2-selection').removeClass('select-has-error');
      form.find('.form-group').removeClass('has-error');
      form.find('input, select, button').prop('disabled', true);
    },
    success: (response) => {
      let icon = 'error';
      let message = '';

      if (response.status === 201) {
        let propertyId = response.data.property_id ?? null;
        icon = 'success';
        setTimeout(() => {
          form[0].reset();
          form.find('input, select, button').prop('disabled', false);
          form.find('#property').val(propertyId);
          handleShowPaymentHistory(propertyId);
        }, 1000);
      } else if (response.status === 400) {
        form.find('input, select, button').prop('disabled', false);
        $.each(response.data, (key, value) => {
          console.log(form.find(`#${key}`));
          if (key === 'property' || key === 'payment_method') {
            form.find(`#${key}`).parent()
              .find('.select2-selection')
              .addClass('select-has-error');
          }
          form.find(`#${key}`).parent().addClass('has-error');
        });

        message = '<br>Please fill up the required fields.';
        if (response.data.payment_amount) {
          if (response.data.payment_amount.max) {
            let balance = $('#remainingBalance');
            for (let i = 0; i < 3; i++) {
              balance.animate({color: '#dc3545'}, 400);
              balance.animate({color: '#212529'}, 400);
            }
            message += '<br>Invalid Payment Amount.<br>Amount is greater than remaining balance.';
          }
          if (response.data.payment_amount.min) {
            message += '<br>Invalid Payment Amount.<br>Please enter amount greater than 0.';
          }
          if (response.data.payment_amount.invalid) {
            message += '<br>Payment Amount invalid value.';
          }
        }

      } else if (response.status === 500) {
        form.find('input, select, button').prop('disabled', false);
        if (response.data.status) {
          message += '<br>' + response.data.status;
        }
      }

      getToast('', response.status, response.message + message, 5, icon);
    }
  });
}

const handleAddProperty = (e) => {
  e.preventDefault();

  let form = $('#addPropertyForm');
  $.ajax({
    type:'POST',
    url:'/api/addProperty',
    data: form.serialize() + `&addProperty`,
    beforeSend: (response) => {
      form.find('.select2-selection').removeClass('select-has-error');
      form.find('.form-group').removeClass('has-error');
      form.find('input, select, textarea, button').prop('disabled', true);
    },
    success: (response) => {
      let icon = 'error';
      let message = '';

      if (response.status === 201) {
        icon = 'success';
        setTimeout(() => {
          $('#addPropertyModal').modal('hide');
          table.search('').order([]).page.len(20).draw();
          form[0].reset();
          form.find('.select2-project-name').val(null).trigger('change');
          form.find('input, select, textarea, button').prop('disabled', false);
        }, 1000);
      } else if (response.status === 400 || response.status === 409) {
        form.find('input, select, textarea, button').prop('disabled', false);

        let fieldError = null;
        $.each(response.data, (key, data) => {
          if (key == 'fields') {
            $.each(data, (index, value) => {
              if (index === 'property_project_name') {
                form.find('.select2-selection').addClass('select-has-error');
              }
              form.find(`#${index}`).parent().addClass('has-error');
              fieldError = 'Please fill up the required fields.';
            });
          } else if (key === 'exists') {
            if (data.length > 0) {
              form.find('.select2-selection').addClass('select-has-error');
              form.find('#property_project_name, #property_block, #property_lot').parent().addClass('has-error');
            }
            message = fieldError !== null ? fieldError : data;
          }
        });
      }

      getToast('', response.status, response.message + '<br>' + message, 5, icon);
    }
  });
}

const handleGetClientPropertySelections = (selector, id) => {
  $(selector).select2({
    placeholder: $(selector).data('placeholder'),
    allowClear: true,
    ajax: {
      url: '/api/getClientPropertySelections?client_id=' + id,
      delay: 350,
      data: (params) => {
        return { search: params.term};
      },
      processResults: (response) => {
        return response.data;
      }
    }
  })
}

const handleSelectProjectAgent = (id) => {
  $.ajax({
    type:'GET',
    url:'/api/getProjectAgentSelections',
    data: { agent_id : id },
    success: (response) => {
      if (response.status === 200) {
        let data = response.data;
        $('.select2-get-project-agents').select2({
          data: data.results
        });
      } else {
        getToast('', response.status, response.message, 5, 'error');
      }
    }
  });
}

const handleGetPropertiesTable = (id) => {
  const propertiesTable = $('#propertiesTable');

  if ($.fn.DataTable.isDataTable(propertiesTable)) {
    propertiesTable.DataTable().destroy();
  }

  propertiesTable.DataTable({
    'processing': true,
    'serverSide': true,
    'sortable': true,
    'responsive': true,
    'order' : [],
    ajax: {
      url: '/api/getClientProperties',
      type: 'POST',
      data: { client_id: id }
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
}
