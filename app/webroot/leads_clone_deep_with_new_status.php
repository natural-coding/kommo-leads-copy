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

/**
 * @todo Autoload them later ;-)
 */
require_once __DIR__ . '/../src/AmoCloud/LeadsCollectionCopier.php';
require_once __DIR__ . '/../src/AmoCloud/NotesCollectionCopierAndLinker.php';
require_once __DIR__ . '/../src/AmoCloud/TasksCollectionCopierAndLinker.php';

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Collections\NotesCollection;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Filters\Interfaces\HasOrderInterface;
use AmoCRM\Models\LeadModel;
use AmoCloud\LeadsCollectionCopier;
use AmoCloud\NotesCollectionCopierAndLinker;
use AmoCloud\TasksCollectionCopierAndLinker;

use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMApiNoContentException;

use function Framework\Utils\dump_nice;
use function Framework\Utils\dump_nice_l;
use function Framework\Utils\dump_nice_header;

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
         'pipeline_id' => (int) $_ENV['LEADS_DEEP_CLONE_PIPELINE_ID'],
         'status_id' => (int) $_ENV['LEADS_DEEP_CLONE_SRC_STATUS_ID']
      ]
   ])
   ->setOrder('created_at', HasOrderInterface::SORT_ASC)
   ->setPrice((int) $_ENV['LEADS_DEEP_CLONE_EXACT_PRICE']);
   // ->setIds([888]);

/**
 * AmoCRM\Collections\Leads\LeadsCollection;
 */
$source_LeadsColl = new LeadsCollection();
try {
   $source_LeadsColl = $apiClient->leads()->get($filter, [LeadModel::CONTACTS]);
}
catch (AmoCRMApiNoContentException $exception) {
   // Нет сделок. Гасим исключение.
   // https://github.com/amocrm/amocrm-api-php/issues/139
   // print 'AmoCRMApiNoContentException';
}

$report = new \StdClass();
$report->LeadsToCopyCount = $source_LeadsColl->count();
$report->LeadsCopied = 0;
$report->NotesToCopyCount = 0;
$report->NotesCopied = 0;
$report->TasksToCopyCount = 0;
$report->TasksCopied = 0;

if ($source_LeadsColl->count() > 0) {
   $amoLeadsCopier = new LeadsCollectionCopier($apiClient);

   $result_amoLeadsCopier = $amoLeadsCopier->doCopy($source_LeadsColl,(int) $_ENV['LEADS_DEEP_CLONE_DEST_STATUS_ID']);
   $report->LeadsCopied = $result_amoLeadsCopier->copied_leadsCollection->count();
   // dump_nice_l($res->copied_leadsCollection->count());
   // dump_nice($res);

   $amoTasksCollectionCopierAndLinker = new TasksCollectionCopierAndLinker($apiClient);
   /**
    * $result = new \stdClass();
    * $result->tasksCollectionCount = 0;
    * $result->copied_tasksCollectionCount = 0;
   */
   $res = $amoTasksCollectionCopierAndLinker->doCopy($result_amoLeadsCopier->map_id_newId);
   $report->TasksToCopyCount = $res->tasksCollectionCount;
   $report->TasksCopied = $res->copied_tasksCollectionCount;   
   
   $amoNotesCopierAndLinker = new NotesCollectionCopierAndLinker($apiClient);
   /**
    * $result = new \stdClass();
    * $result->notesCollectionCount = 0;
    * $result->copied_notesCollectionCount = 0;
    */
   $res = $amoNotesCopierAndLinker->doCopy($result_amoLeadsCopier->map_id_newId);
   $report->NotesToCopyCount = $res->notesCollectionCount;
   $report->NotesCopied = $res->copied_notesCollectionCount;

}

print '<pre>';
printf(
   'Leads to copy (pipeline %s, status %s): %d<br>',   
   $_ENV['LEADS_DEEP_CLONE_PIPELINE_ID'],
   $_ENV['LEADS_DEEP_CLONE_SRC_STATUS_ID'],
   $report->LeadsToCopyCount
);

printf(
   'Leads copied (pipeline %s, new status %s): %d<br>',
   $_ENV['LEADS_DEEP_CLONE_PIPELINE_ID'],
   $_ENV['LEADS_DEEP_CLONE_DEST_STATUS_ID'],
   $report->LeadsCopied
);


printf('Notes to copy: %d<br>',$report->NotesToCopyCount);
printf('Notes copied: %d<br>',$report->NotesCopied);

printf('Tasks to copy: %d<br>',$report->TasksToCopyCount);
printf('Tasks copied: %d<br>',$report->TasksCopied);
print '</pre>';
/*print '<pre>';
printf(
   'Выбрано сделок для копирования из воронки %s на этапе %s: %d<br>',
   $_ENV['LEADS_DEEP_CLONE_PIPELINE_ID'],
   $_ENV['LEADS_DEEP_CLONE_SRC_STATUS_ID'],
   $report->LeadsToCopyCount
);

printf(
   'Скопировано сделок в воронке %s на этап %s: %d<br>',
   $_ENV['LEADS_DEEP_CLONE_PIPELINE_ID'],
   $_ENV['LEADS_DEEP_CLONE_DEST_STATUS_ID'],
   $report->LeadsCopied
);


printf('Выбрано примечаний для копирования: %d<br>',$report->NotesToCopyCount);
printf('Примечаний скопировано: %d<br>',$report->NotesCopied);

printf('Выбрано задач для копирования: %d<br>',$report->TasksToCopyCount);
printf('Задач скопировано: %d<br>',$report->TasksCopied);
print '</pre>';*/
?>
</body>
</html>