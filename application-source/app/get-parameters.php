
<?php
  // Retrieve database settings from AWS Secrets Manager
  error_log('Retrieving database settings');

  require 'aws.phar';
  session_start();

  $region = 'us-east-1';

  $secretArn = 'arn:aws:secretsmanager:us-east-1:544611252083:secret:Oyama-Capstone-Database-Credentials-JF3wA0';

  $databaseEndpoint = 'oyama-capstone-mysql.c81ms2ku4rww.us-east-1.rds.amazonaws.com';

  try {
    // Create Secrets Manager client
    $secretsClient = new Aws\SecretsManager\SecretsManagerClient([
      'version' => 'latest',
      'region'  => $region
    ]);

    // Retrieve database credentials
    $result = $secretsClient->getSecretValue([
      'SecretId' => $secretArn
    ]);

    // Decode the secret
    $credentials = json_decode($result['SecretString'], true);

    if (
      !is_array($credentials) ||
      !isset($credentials['username']) ||
      !isset($credentials['password'])
    ) {
      throw new RuntimeException('Database credentials are missing or invalid.');
    }

    // Store database settings in the existing session variables
    $_SESSION['ep'] = $databaseEndpoint;
    $_SESSION['un'] = $credentials['username'];
    $_SESSION['pw'] = $credentials['password'];
    $_SESSION['db'] = 'countries';

  } catch (Throwable $e) {

    error_log('Database configuration error: ' . $e->getMessage());

    $_SESSION['ep'] = '';
    $_SESSION['un'] = '';
    $_SESSION['pw'] = '';
    $_SESSION['db'] = '';
  }
?>
