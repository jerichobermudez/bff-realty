<?php
  // Check fields
  function checkField($field, $isDate = false) {
    $value = null;

    if (!empty($field)) {
      $value = $isDate ? date('Y-m-d', strtotime($field)) : $field;
    }

    return $value;
  }

  // ValidateFields
  function validateFields($data, $fields) {
    $validations = [];
    foreach ($fields as $key => $message) {
      if ($key === 'marital_status') {
        if ($data[$key] === 'Married' && empty($data['spouse_name'])) {
          $validations['spouse_name'] = 'This field is required.';
        }
        if (trim($data[$key]) === '') {
          $validations[$key] = $message;
        }
      } else {
        if (trim($data[$key]) === '') {
          $validations[$key] = $message;
        }
      }
    }

    return $validations;
  }