<!DOCTYPE html>
<html>
<head>
   <title>project-management_doc - List</title>
   <style type="text/css">
      a, a:visited {
         color: blue;
      }
   </style>
   </script>
</head>
<body>
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

printf('<h2>Welcome to website, %s!</h2>',$accountModel->getName());

print<<<EndMarker
<h3>
Would you like to run
<a href="http://localhost/leads_update_pipeline_status.php" target="_blank">leads_update_pipeline_status.php</a>
or
<a href="http://localhost/leads_clone_deep_with_new_status.php" target="_blank">leads_clone_deep_with_new_status.php</a> scripts?
</h3>
EndMarker;

$extraInfo=<<<EndMarker
<hr style="margin-top: 3rem; margin-bottom: 1rem">
<pre>
Technical information
=====================
</pre>
EndMarker;

print $extraInfo;

dump_nice_l($accountModel->toArray());

print<<<EndMarker
<p style="font-size: 0.7rem">In order to create an access token file, please follow these steps:
1) Set AMOCRM_AUTHORIZATION_CODE variable in the .env.sensitive.data file.
2) Follow the http://localhost/_create_access_token_file.php link.</p>
EndMarker;
?>
</body>
</html>