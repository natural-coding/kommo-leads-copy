<?php
namespace AmoCloud;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Collections\NotesCollection;
use AmoCRM\Models\LeadModel;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Filters\NotesFilter;
use AmoCRM\Models\Factories\NoteFactory;
use AmoCRM\Models\NoteType\CommonNote;
use AmoCRM\Filters\Interfaces\HasOrderInterface;

use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMApiNoContentException;

use function Framework\Utils\dump_nice;
use function Framework\Utils\dump_nice_l;
use function Framework\Utils\dump_nice_header;

class NotesCollectionCopierAndLinker
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
      $result->notesCollectionCount = 0;
      $result->copied_notesCollectionCount = 0;

      $entityIds = [];

      foreach($p_map_id_newId as $key=>$val)
         array_push($entityIds,$key);

      $leadNotesService = $this->apiClient->notes(EntityTypesInterface::LEADS);

      $notesFilter = (new NotesFilter())
         ->setEntityIds($entityIds)
         ->setNoteTypes([NoteFactory::NOTE_TYPE_CODE_COMMON])
         ->setOrder('created_at', HasOrderInterface::SORT_ASC);

      try {
         $notesColl = $leadNotesService->get($notesFilter);
      }
      catch (AmoCRMApiNoContentException $exception) {
         // Нет notes. Копировать нечего
         // https://github.com/amocrm/amocrm-api-php/issues/139
         // print 'AmoCRMApiNoContentException<br>';
         return $result;
      }

      $result->notesCollectionCount = $notesColl->count();

      // dump_nice([$notesColl]);
      // dump_nice($notesColl->first()->getNoteType());

      $new_NotesColl = new NotesCollection();
      foreach($notesColl as $noteModel)
      {
         if ($noteModel->getNoteType() === NoteFactory::NOTE_TYPE_CODE_COMMON) {
            $entity_id = $noteModel->getEntityId();
            if (array_key_exists($entity_id,$p_map_id_newId)) {
               $new_Note = (new CommonNote())
                  ->setEntityId($p_map_id_newId[$entity_id])
                  ->setResponsibleUserId($noteModel->getResponsibleUserId())
                  ->setGroupId($noteModel->getGroupId())
                  ->setAccountId($noteModel->getAccountId())
                  ->setIsNeedToTriggerDigitalPipeline($noteModel->getIsNeedToTriggerDigitalPipeline())
                  ->setText($noteModel->getText());

               $new_NotesColl->add($new_Note);
            }
         }
      }

      $created_Notes = new NotesCollection();

      if ($new_NotesColl->count() > 0)
         $created_Notes = $leadNotesService->add($new_NotesColl);

      $result->copied_notesCollectionCount = $created_Notes->count();
      return $result;
   }
}   