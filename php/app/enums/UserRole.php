<?php  
  abstract class UserRole
  {
    const ADMIN = 1;
    const USER = 2;

    public static function getTextValue(int $role)
    {
      $userRoleValue = [
        static::ADMIN => 'Admin',
        static::USER => 'User'
      ];

      $textValue = $userRoleValue[$role] ?? '------';

      return $textValue;
    }
  }
      
  $userRoleReflectionClass = new ReflectionClass('UserRole');
  $userRole = $userRoleReflectionClass->getConstants();
