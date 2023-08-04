const handleLogin = (e) => {
  e.preventDefault();

  let form = $('#loginForm');
  $.ajax({
    type:'POST',
    url:'/api/login',
    data: form.serialize() + `&login`,
    beforeSend: (response) => {
      form.find('.form-group').removeClass('has-error');
    },
    success: (response) => {
      let icon = '';
      let message = '';

      if (response.status === 200) {
        icon = 'success';
        message = '<br>Successfully Login!';
        setTimeout(() => {
          window.location = response.data.url ?? '/';
        }, 1000);
      } else if (response.status === 400) {
        icon = 'error';
        message = '';
        $.each(response.data, (key, value) => {
          form.find(`#${key}`).parent().addClass('has-error');
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

const getToast = (header, status, message, topPos = null, icon) => {
  $.toast({
    heading: header,
    text: status + ': ' + message,
    position: {
      left : 'auto',
      right : 5,
      top : topPos ?? 65,
      bottom : 'auto'
    },
    icon: icon,
    loader: true,
    loaderBg: '#231F20'
  });
}