<!DOCTYPE html>
<html>
<head>
   <title>project-management_doc - List</title>
   <style type="text/css">
   </style>
   </script>
</head>
<body>
<?php

require_once __DIR__ . '/../src/bootstrap.php';

use AmoCRM\Client\AmoCRMApiRequest;
use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\LeadModel;
use function Framework\Utils\dump_nice;

// dump_nice(get_class_methods(new LeadsCollection()));
// dump_nice(get_class_methods(new LeadModel()));


/**
 * Copy-past to test approach.
 * Refactor it later! ;-)
 */
$apiClient = new AmoCRMApiClient(
   $_ENV['AMOCRM_CLIENT_ID'],
   $_ENV['AMOCRM_CLIENT_SECRET'],
   $_ENV['AMOCRM_CLIENT_REDIRECT_URI']
);

$apiClient->setAccountBaseDomain($_ENV['AMOCRM_SUB_DOMAIN'])
   ->setAccessToken(Framework\Utils\getAccessToken());

$filter = (new LeadsFilter())
   ->setStatuses([
      [
         'pipeline_id' => (int) $_ENV['LEADS_UPDATE_PIPELINE_ID'],
         'status_id' => (int) $_ENV['LEADS_UPDATE_SRC_STATUS_ID']
      ]
   ]);

/**
 * IntOrIntRangeFilterTrait::parseIntOrIntRangeFilter
 * НЕ поддерживает диапазоны на текущий момент (05.07.2023)
 */
$filterPrice_LeadsColl = $apiClient->leads()->get($filter);
$changeStatus_LeadsColl = new LeadsCollection();

$PRICE_FROM = (int) $_ENV['LEADS_UPDATE_PRICE_FROM'];

foreach($filterPrice_LeadsColl as $leadModel) {
   if ($leadModel->getPrice() >= $PRICE_FROM)
      $changeStatus_LeadsColl->add($leadModel);
}

// Меняем статус сделок
foreach($changeStatus_LeadsColl as $leadModel)
   $leadModel->setStatusId((int) $_ENV['LEADS_UPDATE_DEST_STATUS_ID']);

if ($changeStatus_LeadsColl->count() > 0)
   $updatedLeads_LeadsColl = $apiClient->leads()->update($changeStatus_LeadsColl);

print '<pre>';
printf(
   'Leads selected (pipeline %s, status %s): %d<br>',
   $_ENV['LEADS_UPDATE_PIPELINE_ID'],
   $_ENV['LEADS_UPDATE_SRC_STATUS_ID'],
   $filterPrice_LeadsColl->count()
);
printf('Items to modify: %d<br>',$changeStatus_LeadsColl->count());
printf('Items changed: %d<br>', isset($updatedLeads_LeadsColl) ? $updatedLeads_LeadsColl->count() : 0);
print '</pre>';

/*print '<pre>';
printf(
   'Выбрано сделок из воронки %s на этапе %s: %d<br>',
   $_ENV['LEADS_UPDATE_PIPELINE_ID'],
   $_ENV['LEADS_UPDATE_SRC_STATUS_ID'],
   $filterPrice_LeadsColl->count()
);
printf('Отобрано сделок для изменения: %d<br>',$changeStatus_LeadsColl->count());
printf('Изменено элементов: %d<br>', isset($updatedLeads_LeadsColl) ? $updatedLeads_LeadsColl->count() : 0);
print '</pre>';*/
?>
</body>
</html>