<?php  
  abstract class PropertyStatus
  {
    const ACTIVE = 1;
    const FULLY_PAID = 2;
    const RESERVED = 3;

    public static function getTextValue(int $status): string
    {
      $propertyStatusValue = [
        static::ACTIVE => 'Active',
        static::FULLY_PAID => 'Fully Paid',
        static::RESERVED => 'Reserved',
      ];

      $textValue = $propertyStatusValue[$status] ?? '------';

      return $textValue;
    }
  }
      
  $propertyReflectionClass = new ReflectionClass('PropertyStatus');
  $propertyStatus = $propertyReflectionClass->getConstants();
