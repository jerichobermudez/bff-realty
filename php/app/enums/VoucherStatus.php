<?php  
  abstract class VoucherStatus
  {
    const AVAILABLE = 1;
    const NOT_AVAILABLE = 2;
    const EXPIRED = 3;

    public static function getTextValue(int $status)
    {
      $voucherStatusValue = [
        static::AVAILABLE => 'Available',
        static::NOT_AVAILABLE => 'Unavailable',
        static::EXPIRED => 'Expired'
      ];

      $textValue = $voucherStatusValue[$status] ?? '------';

      return $textValue;
    }
  }
      
  $voucherStatusReflectionClass = new ReflectionClass('VoucherStatus');
  $voucherStatus = $voucherStatusReflectionClass->getConstants();
