var currentTab = 0;

$(document).ready(() => {
  showTab(currentTab);

  handleGetProjectSelections('.select2-project-name', '#project_location');

  $('#property_lot_area, #property_price_per_sqm, #terms_of_payment, #downpayment_amount').on('change, input', () => { handleComputeMonthlyAmortization(); });
});

const showTab = (n) => {
  let tab = $('.tab');
  let prevBtn = $('#prevBtn');
  let nextBtn = $('#nextBtn');

  $(tab[n]).show();

  let isPrevDisplay = n == 0 ? 'none' : 'inline';
  prevBtn.css('display', isPrevDisplay);

  let nextBtnText = n == (tab.length - 1) ? 'Submit' : 'Next';
  nextBtn.html(nextBtnText);

  fixStepIndicator(n)
}

const nextPrev = (n) => {
  let tab = $('.tab');
  let step = $('.step');
  let stepTitle = $('.step-title');

  if (n === 1 && !validateForm()) return false;

  $(tab[currentTab]).hide();

  currentTab = currentTab + n;
  $(step[currentTab]).removeClass('finish').addClass('active');
  $(stepTitle[currentTab]).removeClass('finish').addClass('active');

  if (currentTab >= tab.length) {
    if (currentTab === 3) currentTab = 2;

    $('#addClientForm').submit();
  }

  showTab(currentTab);
}

const validateForm = () => {
  let valid = true;
  let tab = $('.tab');
  let field = $(tab[currentTab]).find('.field-required');

  for (let i = 0; i < field.length; i++) {
    if ($(field[i]).val() === '') {
      $(field[i]).addClass('is-invalid');
      valid = false;
    }
  }

  if (valid) {
    if (currentTab < 2) {
      $('.step').eq(currentTab).addClass('finish');
      $('.step-title').eq(currentTab).addClass('finish');
    }
  } else {
    $('html, body').animate({
      scrollTop: $(tab).eq(currentTab).find('.is-invalid').eq(0).offset().top - 220
    }, 300);
    $(tab[currentTab]).find('.is-invalid').eq(0).focus();
    getToast('', 400, 'Bad Request<br>Please fill up the required fields.', 5, 'error');
  }

  return valid;
}

const fixStepIndicator = (n) => {
  let step = $('.step');
  let stepTitle = $('.step-title');

  for (let i = 0; i < step.length; i++) {
    $(step).removeClass('active');
    $(stepTitle).removeClass('active');
  }

  $(step).eq(n).addClass('active');
  $(stepTitle).eq(n).addClass('active');
}

const handleFormInput = (e) => {
  let targetDiv = $(e.target);
  targetDiv.removeClass('is-invalid');

  if (targetDiv.attr('id') === 'marital_status' && targetDiv.val() === 'Married') {
    $('.spouse-details #spouse_name').addClass('field-required');
  } else {
    $('.spouse-details #spouse_name').removeClass('field-required is-invalid');
  }
}

const handleAddClient = (e) => {
  e.preventDefault();

  let form = $('#addClientForm');
  $.ajax({
    type:'POST',
    url:'/api/addClient',
    data: form.serialize() + `&addClient`,
    beforeSend: (response) => {
      form.find('input, select').removeClass('is-invalid');
      form.find('input, textarea, button, select').prop('disabled', true);
    },
    success: (response) => {
      let icon = 'error';
      let message = '';

      if (response.status === 201) {
        icon = 'success';
        setTimeout(() => {
          window.location.reload();
          form.find('input, textarea, button, select').prop('disabled', false);
        }, 1000);
      } else if (response.status === 400 || response.status === 409) {
        form.find('input, select, textarea, button').prop('disabled', false);

        let fieldError = null;
        $.each(response.data, (key, data) => {
          if (key == 'fields') {
            $.each(data, (index, value) => {
              form.find(`#${index}`).addClass('is-invalid');
              fieldError = 'Please fill up the required fields.';
            });
          } else if (key === 'exists') {
            if (data.length > 0) {
              form.find('#property_project_name, #property_block, #property_lot').addClass('is-invalid');
            }
            message = fieldError !== null ? fieldError : data;
          }
        });
      }

      getToast('', response.status, response.message + '<br>' + message, 5, icon);
    }
  });
}

$('.datetimepicker-input').datetimepicker({
  'format': 'L',
  format: 'YYYY-MM-DD'
});
