<?php
namespace AmoCloud;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Models\LeadModel;
use function Framework\Utils\dump_nice;
use function Framework\Utils\dump_nice_l;

class LeadsCollectionCopier
{
   private AmoCRMApiClient $apiClient;

   private static function AddIdToLeadName(string $p_name, int $p_leadId) : string
   {
      return $p_name . ' [#' . $p_leadId . ']';
   }

   private static function ExtractIdFromLeadName(string $p_nameWithId) : ?int
   {
      $result = null;

      $leftPos = strrpos($p_nameWithId,'[#');

      if ($leftPos !== false) {
         $leftPos += 2; // 2 is length of '[#'
         $rightPos = strrpos($p_nameWithId,']',$leftPos); 

         if ($rightPos !== false) {
            $len = $rightPos - $leftPos;
            if ($len > 0) {
               $num = substr($p_nameWithId, $leftPos, ($rightPos - $leftPos));
               $result = intval($num);
            }
         }
      }

      return $result;
   }

   public function __construct($p_apiClient)
   {
      if (is_null($p_apiClient))
         throw new Exception('LeadsCollectionCopier::__construct', 999);
         
      $this->apiClient = $p_apiClient;
   }
   public function doCopy(LeadsCollection $p_src_LeadsColl, int $p_dest_statusId) : \stdClass
   {
      $new_leadsCollection = new LeadsCollection();

      foreach($p_src_LeadsColl as $src_LeadModel)
      {
         $lead = new LeadModel();
         $lead->setName(
               LeadsCollectionCopier::AddIdToLeadName(
                  $src_LeadModel->getName(), $src_LeadModel->getId()
                  )
               )
               ->setPrice($src_LeadModel->getPrice())
               ->setPipelineId($src_LeadModel->getPipelineId())
               ->setStatusId($p_dest_statusId)
               ->setAccountId($src_LeadModel->getAccountId())
               ->setGroupId($src_LeadModel->getGroupId())
               ->setResponsibleUserId($src_LeadModel->getResponsibleUserId())
               ->setClosedAt($src_LeadModel->getClosedAt())
               ->setTags($src_LeadModel->getTags())
               ->setSourceId($src_LeadModel->getSourceId())
               ->setCompany($src_LeadModel->getCompany())
               ->setCustomFieldsValues($src_LeadModel->getCustomFieldsValues())
               ->setContacts($src_LeadModel->getContacts())
               ->setScore($src_LeadModel->getScore())
               ->setCatalogElementsLinks($src_LeadModel->getCatalogElementsLinks())
               ->setMetadata($src_LeadModel->getMetadata())
               ->setIsMerged($src_LeadModel->isMerged());

            $new_leadsCollection->add($lead);
      }

      $copied_leadsCollection = new LeadsCollection();

      if ($new_leadsCollection->count())
         $copied_leadsCollection = $this->apiClient->leads()->add($new_leadsCollection);

      /**
       * Let's create correspondence between old lead id and new lead id (associative array)
       */
      $map_id_newId = array();

      foreach($copied_leadsCollection as $copied_LeadModel)
      {
         $newId = $copied_LeadModel->getId();
         $id = self::ExtractIdFromLeadName($copied_LeadModel->getName());
         if (!is_null($id))
            $map_id_newId[$id] = $newId;
      }

      $result = new \stdClass();
      $result->copied_leadsCollection = $copied_leadsCollection;
      $result->map_id_newId = $map_id_newId;

      return $result;
   }
}