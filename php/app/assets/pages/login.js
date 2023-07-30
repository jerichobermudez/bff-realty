const handleLogin = (e) => {
  e.preventDefault();

  let form = $('#loginForm');
  $.ajax({
    type:'POST',
    url:'/api/login',
    data: form.serialize() + `&login`,
    beforeSend: (response) => {
      form.find('input').removeClass('is-invalid');
    },
    success: (response) => {
      let icon = '';
      let message = '';

      if (response.status === 200) {
        icon = 'success';
        message = '<br>Successfully Login!';
        setTimeout(() => {
          window.location = 'dashboard';
        }, 1500);
      } else if (response.status === 400) {
        icon = 'error';
        message = '';
        $.each(response.data, (key, value) => {
          form.find(`#${key}`).addClass('is-invalid');
          message += `<br>${key.replace(/_/g, ' ').toLowerCase().replace(/(?: |\b)(\w)/g, (key) => {
            return key.toUpperCase();    
          })} ${value}`;
        });

        message = message.replace(/This /g, '');

      } else if (response.status === 401) {
        icon = 'error';
        message = '<br>Invalid login credentials.';
      }

      getToast('', response.status, response.message + message, 5, icon);
    }
  });
}