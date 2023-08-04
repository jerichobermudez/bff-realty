let table = $('#vouchersTable').DataTable({
  'processing': true,
  'serverSide': true,
  'sortable': true,
  'responsive': true,
  'order' : [],
  ajax: {
    url: '/api/getVouchers',
    type: 'POST',
  },
  'displayLength': 20,
  'lengthMenu': [
    [10, 20, 50, 100],
    [10, 20, 50, 100]
  ],
  'columnDefs': [
    {
      'targets': [6],
      'orderable': false,
    }
  ],
  scrollCollapse: false,
  scrollX: 500,
  scrollY: false
});

const generateVoucherCode = (e) => {
  e.preventDefault();
  let div = $(e.target);
  
  $.ajax({
    type:'POST',
    url:'/api/generateVoucher',
    data: $.param({'generateVoucher': true}),
    beforeSend: (response) => {
      div.prop('disabled', true);
    },
    success: (response) => {
      table.search('').order([]).page.len(20).draw();
      setTimeout(() => {
        div.prop('disabled', false);
      }, 1500);
    }
  });
}
const handleRemoveVoucher = (id) => {
  if (!id) return;

  let message = 'Are you sure you want to remove voucher code with ID: ' + id + "?\nThis action cannot be undone.";
  if (confirm(message)) {
    $.ajax({
      type: 'POST',
      url: '/api/removeVoucher',
      data: $.param({ voucher_id: id }) + `&removeVoucher`,
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