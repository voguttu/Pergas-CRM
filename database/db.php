<?php

$dbHost = getenv('PERGASCRM_DB_HOST') ?: 'localhost';
$dbUsername = getenv('PERGASCRM_DB_USERNAME') ?: '';
$dbPassword = getenv('PERGASCRM_DB_PASSWORD') ?: '';
$dbName = getenv('PERGASCRM_DB_NAME') ?: '';

$localConfigPath = getenv('PERGASCRM_LOCAL_DB_CONFIG');
if ($localConfigPath !== false && is_file($localConfigPath)) {
  $localDbConfig = require $localConfigPath;
  if (!is_array($localDbConfig)) {
    http_response_code(500);
    error_log('Pergas CRM local database config must return an array.');
    exit('Database configuration is invalid.');
  }
  $dbHost = $localDbConfig['host'] ?? $dbHost;
  $dbUsername = $localDbConfig['username'] ?? $dbUsername;
  $dbPassword = $localDbConfig['password'] ?? $dbPassword;
  $dbName = $localDbConfig['database'] ?? $dbName;
}

if ($dbUsername === '' || $dbName === '') {
  http_response_code(500);
  error_log('Pergas CRM database settings are missing.');
  exit('Database configuration is missing. Set the PERGASCRM_DB_* environment variables or PERGASCRM_LOCAL_DB_CONFIG.');
}

$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
if ($conn->connect_error) {
  http_response_code(500);
  error_log('Pergas CRM database connection failed: ' . $conn->connect_error);
  exit('Database connection failed. Check the server configuration.');
}

?>
