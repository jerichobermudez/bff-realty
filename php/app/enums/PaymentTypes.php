<?php  
  abstract class PaymentTypes
  {
    const DOWNPAYMENT = 1;
    const MONTHLY_AMORTIZATION = 2;
    const HOLDING_FEE = 3;
    const PARTIAL_IN_DOWNPAYMENT = 4;
    const PARTIAL_PAYMENT = 5;
    const FULL_PAYMENT = 6;
    const TRANSFER_TO_OTHER_PROJECT = 7;
    const REFUND = 8;

    public static function getTextValue(int $paymentType): string
    {
      $paymentTypes = [
        static::DOWNPAYMENT => 'Downpayment',
        static::MONTHLY_AMORTIZATION => 'Monthly Amortization',
        static::HOLDING_FEE => 'Holding Fee',
        static::PARTIAL_IN_DOWNPAYMENT => 'Partial in Downpayment',
        static::PARTIAL_PAYMENT => 'Partial Payment',
        static::FULL_PAYMENT => 'Full Payment',
        static::TRANSFER_TO_OTHER_PROJECT => 'Transfer to other Project',
        static::REFUND => 'Refund',
      ];

      $textValue = $paymentTypes[$paymentType] ?? 'Unknown Payment Type';

      return $textValue;
    }
  }
      
  $paymentReflectionClass = new ReflectionClass('PaymentTypes');
  $paymentTypes = $paymentReflectionClass->getConstants();
