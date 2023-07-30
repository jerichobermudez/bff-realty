<?php
  // Define the array of allowed IP addresses
  $allowedIPs = [
    '192.168.1.1',
    '172.25.0.1',
    'your_ip_address_here'
  ];

  // Get the visitor's IP address
  $visitorIP = $_SERVER['REMOTE_ADDR'];

  // Check if the visitor's IP is in the allowed IP array
  if (!in_array($visitorIP, $allowedIPs)) {
    die('Access denied.');
  }
?>