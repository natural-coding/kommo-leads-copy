<?php

require_once __DIR__ . '/../src/bootstrap.php';

use AmoCRM\Client\AmoCRMApiClient;

if (!isset($_GET['code']))
   die;

$apiClient = new AmoCRMApiClient(
   $_ENV['AMOCRM_CLIENT_ID'],
   $_ENV['AMOCRM_CLIENT_SECRET'],
   $_ENV['AMOCRM_CLIENT_REDIRECT_URI']
);

$apiClient->setAccountBaseDomain($_ENV['AMOCRM_SUB_DOMAIN']);

$token = $apiClient->getOAuthClient()->getAccessTokenByCode($_GET['code']);

file_put_contents($_ENV['APP_TOKEN_FILE'], json_encode($token->jsonSerialize(), JSON_PRETTY_PRINT));