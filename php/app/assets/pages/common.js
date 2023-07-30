const handleGetProjectSelections = (selector, locationSelector) => {
  $(selector).select2({
    placeholder: $(selector).data('placeholder'),
    allowClear: true,
    theme: 'bootstrap4',
    ajax: {
      url: '/api/getProjectSelections',
      delay: 350,
      data: (params) => {
        return { search: params.term};
      },
      processResults: function (response) {
        return response.data;
      }
    }
  }).on('change', function (e) {
    var selectedOption = $(this).select2('data')[0];
    $(locationSelector).val(selectedOption.location);
  });
}

const handleComputeMonthlyAmortization = (
  monthlyAmortizationParam = '#monthly_amortization',
  lotAreaParam = '#property_lot_area',
  pricePerSqmParam = '#property_price_per_sqm',
  paymentTermsParam = '#terms_of_payment',
  downpaymentAmountParam = '#downpayment_amount',
) => {
  let monthlyAmortization = $(monthlyAmortizationParam);
  let lotArea = parseFloat($(lotAreaParam).val().replace(/,/g, ''));
  let pricePerSqm = parseFloat($(pricePerSqmParam).val().replace(/,/g, ''));
  let paymentTerms = parseFloat($(paymentTermsParam).val());
  let downpaymentAmount = parseFloat($(downpaymentAmountParam).val().replace(/,/g, ''));
  lotArea = !isNaN(lotArea) ? lotArea : 0;
  pricePerSqm = !isNaN(pricePerSqm) ? pricePerSqm : 0;
  paymentTerms = !isNaN(paymentTerms) ? paymentTerms : 12;
  downpaymentAmount = !isNaN(downpaymentAmount) ? downpaymentAmount : 0;

  // Compute the monthly amortization
  let totalPropertyPrice = lotArea * pricePerSqm;
  let remainingBalance = totalPropertyPrice - downpaymentAmount;
  let totalMonthlyAmortization = remainingBalance / paymentTerms;
  totalMonthlyAmortization = isNaN(totalMonthlyAmortization) || totalMonthlyAmortization <= 0
    ? 0
    : totalMonthlyAmortization;

  // Update the 'monthly_amortization' input field with the computed value
  monthlyAmortization.val(totalMonthlyAmortization.toFixed(2));
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
