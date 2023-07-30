<?php
  function getResponseStatus($code, $data = []) {
    $message = [
      200 => 'OK',
      201 => 'Created',
      400 => 'Bad Request',
      401 => 'Unauthorized',
      403 => 'Forbidden',
      404 => 'Not Found',
      409 => 'Confict',
      500 => 'Internal Server Error'
    ];

    if (array_key_exists($code, $message)) {
        $response = [
          'status' => $code,
          'message' => $message[$code],
          'data' => $data
        ];

        if (empty($data)) unset($response['data']);

        return json_encode($response);
    } else {
      $response = [
        'status' => $code,
        'message' => 'Unknown'
      ];

      return json_encode($response);
    }
  }
