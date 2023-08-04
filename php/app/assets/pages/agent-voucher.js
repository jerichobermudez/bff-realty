$(document).ready(() => {
  $('#inputVoucherModal').modal('show');

  handleGetProjectSelections('.select2-get-projects', '#project_location');

  $('#property_lot_area, #property_price_per_sqm, #terms_of_payment, #downpayment_amount').on('change, input', () => { handleComputeMonthlyAmortization(); });

  $('.select2-get-agents').select2({
    placeholder: $('.select2-get-agents').data('placeholder'),
    allowClear: true,
    ajax: {
      url: '/api/getAgentSelections',
      delay: 250,
      data: (params) => {
        return { search: params.term};
      },
      processResults: function (response) {
        return response.data;
      }
    }
  });
});

const handleSubmitAgentVoucher = (e) => {
  e.preventDefault();

  let form = $(e.target);
  $.ajax({
    type:'POST',
    url:'/api/agentSubmitVoucher',
    data: form.serialize() + `&submitVoucher`,
    beforeSend: (response) => {
      form.find('.form-group').removeClass('has-error');
      form.find('.select2-selection').removeClass('select-has-error');
      form.find('input, button, select').prop('disabled', true);
    },
    success: (response) => {
      let icon = '';
      let message = '';

      if (response.status === 200) {
        icon = 'success';
        message = '<br>Voucher successfully used!';
        setTimeout(() => {
          window.location = '/agent';
        }, 1500);
      } else if (response.status === 400) {
        icon = 'error';
        message = '';
        $.each(response.data, (key, value) => {
          form.find(`#${key}`).parent().addClass('has-error');
          if (key === 'user_id') {
            form.find('.select2-selection').addClass('select-has-error');
          }
          message += `<br>${value}`;
        });
        form.find('input, button, select').prop('disabled', false);

      } else if (response.status === 401) {
        icon = 'error';
        message = '<br>Invalid login credentials.';
        form.find('input, button, select').prop('disabled', false);
      } else {
        form.find('input, button, select').prop('disabled', false);
      }

      getToast('', response.status, response.message + message, 5, icon);
    }
  });
}

const handleReserveClientForm = (e) => {
  e.preventDefault();

  let form = $(e.target);
}