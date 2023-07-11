<?php
namespace AmoCloud;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Models\TaskModel;
use AmoCRM\Collections\TasksCollection;
use AmoCRM\Filters\TasksFilter;
use AmoCRM\Filters\Interfaces\HasOrderInterface;
use AmoCRM\Helpers\EntityTypesInterface;

use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMApiNoContentException;
use AmoCRM\Exceptions\AmoCRMApiErrorResponseException;

use function Framework\Utils\dump_nice;
use function Framework\Utils\dump_nice_l;
use function Framework\Utils\dump_nice_header;

class TasksCollectionCopierAndLinker
{
   private AmoCRMApiClient $apiClient;

   public function __construct($p_apiClient)
   {
      if (is_null($p_apiClient))
         throw new Exception('LeadsCollectionCopier::__construct', 888);
         
      $this->apiClient = $p_apiClient;
   }

   public function doCopy(array $p_map_id_newId) : \stdClass
   {
      $result = new \stdClass();
      $result->tasksCollectionCount = 0;
      $result->copied_tasksCollectionCount = 0;

      $entityIds = [];

      foreach($p_map_id_newId as $key=>$val)
         array_push($entityIds,$key);

      $tasksService = $this->apiClient->tasks();

      $tasksFilter = (new TasksFilter())
         ->setEntityType(EntityTypesInterface::LEADS)
         ->setEntityIds($entityIds)
         ->setOrder('created_at', HasOrderInterface::SORT_ASC);

      try {
         $tasksColl = $tasksService->get($tasksFilter);
      }
      catch (AmoCRMApiNoContentException $exception) {
         // Нет задач вообще. Копировать нечего
         // https://github.com/amocrm/amocrm-api-php/issues/139
         // print 'AmoCRMApiNoContentException<br>';
         return $result;
      }

      $result->tasksCollectionCount = $tasksColl->count();

      $new_TasksColl = new TasksCollection();
      foreach($tasksColl as $taskModel)
      {
         $entity_id = $taskModel->getEntityId();
         if (array_key_exists($entity_id,$p_map_id_newId)) {
            $new_TaskModel = (new TaskModel())
               ->setEntityId($p_map_id_newId[$entity_id])
               ->setEntityType($taskModel->getEntityType())
               ->setCompleteTill($taskModel->getCompleteTill())
               ->setDuration($taskModel->getDuration())
               ->setResponsibleUserId($taskModel->getResponsibleUserId())
               ->setText('[Создана при копировании сделки ' . $entity_id . ']' . $taskModel->getText() ?? '(Задача без текста)')
               ->setTaskTypeId($taskModel->getTaskTypeId())

               ->setAccountId($taskModel->getAccountId())
               ->setIsCompleted($taskModel->getIsCompleted())
               ->setResult($taskModel->getResult())

               ->setGroupId($taskModel->getGroupId());
               // ->setCreatedBy($taskModel->getCreatedBy())
               // ->setUpdatedBy($taskModel->getUpdatedBy())
            
            $new_TasksColl->add($new_TaskModel);
         }
      }
      // dump_nice([$tasksColl]);
      // dump_nice([$new_TasksColl]);

      $created_Tasks = new TasksCollection();

      if ($new_TasksColl->count() > 0) {
         try {
            $created_Tasks = $tasksService->add($new_TasksColl);
         }
         catch (AmoCRMApiErrorResponseException $exception) {
            dump_nice_header('AmoCRM\Exceptions\AmoCRMApiNoContentException => object dump real |AmoCRMApiErrorResponseException|');
            dump_nice([$exception]);
         }
      }

      $result->copied_tasksCollectionCount = $created_Tasks->count();
      return $result;

      // dump_nice($created_Tasks->count());

      // dump_nice([$tasksColl]);
      // dump_nice([$tasksColl->last()]);
   }
}