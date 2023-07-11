<?php

/*print '<pre>';
print_r($_SERVER);
die;
*/
require_once __DIR__ . '/../src/bootstrap.php';

use AmoCRM\Client\AmoCRMApiClient;

use function Framework\Utils\dump_nice;
use function Framework\Utils\dump_nice_l;


$apiClient = new AmoCRMApiClient(
   $_ENV['AMOCRM_CLIENT_ID'],
   $_ENV['AMOCRM_CLIENT_SECRET'],
   $_ENV['AMOCRM_CLIENT_REDIRECT_URI']
);

$apiClient->setAccountBaseDomain($_ENV['AMOCRM_SUB_DOMAIN'])
   ->setAccessToken(Framework\Utils\getAccessToken());

$accountModel = $apiClient->account()->getCurrent();

printf('<h2>Добро пожаловать, %s!</h2>',$accountModel->getName());

print<<<EndMarker
<h3>
Желаете выполнить скрипт
<a href="http://localhost/leads_update_pipeline_status.php" target="_blank">leads_update_pipeline_status.php</a>
или
<a href="http://localhost/leads_clone_deep_with_new_status.php" target="_blank">leads_clone_deep_with_new_status.php</a>?
</h3>
EndMarker;

$extraInfo=<<<EndMarker
<hr style="margin-top: 3rem; margin-bottom: 1rem">
<pre>
Техническая информация
======================
</pre>
EndMarker;

print $extraInfo;

dump_nice_l($accountModel->toArray());

print<<<EndMarker
<p style="font-size: 0.7rem">Для создания файла с токеном доступа, перейдите по ссылке <span>http://localhost/_create_access_token_file.php<span>, предварительно обновив значение переменной AMOCRM_AUTHORIZATION_CODE в исходном коде приложения.</p>
EndMarker;