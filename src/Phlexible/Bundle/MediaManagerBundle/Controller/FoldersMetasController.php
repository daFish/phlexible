<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Folder meta controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_MEDIA')")
 * @Rest\NamePrefix("phlexible_api_mediamanager_")
 */
class FoldersMetasController extends FOSRestController
{
    /**
     * @param Request $request
     * @param string  $folderId
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a collection of FolderMeta",
     *   section="mediamanager",
     *   resource=true,
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */
    public function getMetasAction(Request $request, $folderId)
    {
        $folder = $this->get('phlexible_media_manager.volume_manager')->getByFolderId($folderId)->findFolder($folderId);

        $folderMetaSetResolver = $this->get('phlexible_media_manager.folder_meta_set_resolver');
        $folderMetaDataManager = $this->get('phlexible_media_manager.folder_meta_data_manager');
        $optionResolver = $this->get('phlexible_meta_set.option_resolver');

        $meta = [];

        foreach ($folderMetaSetResolver->resolve($folder) as $metaSet) {
            $metaData = $folderMetaDataManager->findByMetaSetAndFolder($metaSet, $folder);

            $fieldDatas = [];

            foreach ($metaSet->getFields() as $field) {
                $options = $optionResolver->resolve($field);

                $fieldData = [
                    'key'          => $field->getName(),
                    'type'         => $field->getType(),
                    'options'      => $options,
                    'readonly'     => $field->isReadonly(),
                    'required'     => $field->isRequired(),
                    'synchronized' => $field->isSynchronized(),
                ];

                if ($metaData) {
                    foreach ($metaData->getLanguages() as $language) {
                        $fieldData["value_$language"] = $metaData->get($field->getName(), $language);
                    }
                }

                $fieldDatas[] = $fieldData;
            }

            $meta[] = [
                'set_id' => $metaSet->getId(),
                'title'  => $metaSet->getName(),
                'fields' => $fieldDatas
            ];
        }

        return [
            'meta' => $meta
        ];
    }

    /**
     * @param Request $request
     * @param string  $folderId
     *
     * @return Response
     */
    public function putMetaAction(Request $request, $folderId)
    {
        $data = $request->get('data');
        $data = json_decode($data, true);

        $metaLanguages = explode(',', $this->container->getParameter('phlexible_meta_set.languages.available'));

        $metaSetManager = $this->get('phlexible_meta_set.meta_set_manager');
        $folderMetaDataManager = $this->get('phlexible_media_manager.folder_meta_data_manager');

        $volume = $this->get('phlexible_media_manager.volume_manager')->getByFolderId($folderId);
        $folder = $volume->findFolder($folderId);

        /*
        $beforeEvent = new BeforeSaveFolderMeta($folder);
        if ($dispatcher->dispatch($beforeEvent)->isPropagationStopped()) {
            $this->getResponse()->setAjaxPayload(
                MWF_Ext_Result::encode(false, null, $beforeEvent->getCancelReason())
            );

            return;
        }
        */

        $metaSetIds = $folder->getAttribute('metasets', []);

        foreach ($data as $metaSetId => $fields) {
            $metaSet = $metaSetManager->find($metaSetId);
            $metaData = $folderMetaDataManager->findByMetaSetAndFolder($metaSet, $folder);

            if (!$metaData) {
                $metaData = $folderMetaDataManager->createFolderMetaData($metaSet, $folder);
            }

            foreach ($fields as $fieldname => $row) {
                foreach ($metaLanguages as $language) {
                    if (!isset($row["value_$language"])) {
                        continue;
                    }

                    if (!$metaSet->hasField($fieldname)) {
                        continue;
                    }

                    // TODO: löschen?
                    if (empty($row["value_$language"])) {
                        continue;
                    }

                    $value = $row["value_$language"];

                    $metaData->set($fieldname, $value, $language);
                }
            }

            $folderMetaDataManager->updateMetaData($metaData);
        }

        /*
        $event = new Media_Manager_Event_SaveFolderMeta($folder);
        $dispatcher->dispatch($event);
        */

        return new ResultResponse(true, 'Folder meta saved.');
    }
}
