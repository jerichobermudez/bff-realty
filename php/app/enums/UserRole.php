<?php  
  abstract class UserRole
  {
    const ADMIN = 1;
    const FINANCE = 2;
    const SALES = 3;
    const AGENT = 4;

    public static function getTextValue(int $role)
    {
      $userRoleValue = [
        static::ADMIN => 'Super Admin',
        static::FINANCE => 'Admin(Finance)',
        static::SALES => 'Admin(Sales)',
        static::AGENT => 'Agent'
      ];

      $textValue = $userRoleValue[$role] ?? '------';

      return $textValue;
    }
  }
      
  $userRoleReflectionClass = new ReflectionClass('UserRole');
  $userRole = $userRoleReflectionClass->getConstants();
