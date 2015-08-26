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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * File meta controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_MEDIA')")
 * @Rest\NamePrefix("phlexible_api_mediamanager_")
 */
class FilesMetasController extends FOSRestController
{
    /**
     * @param Request $request
     * @param string  $fileId
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a collection of FileMeta",
     *   section="mediamanager",
     *   resource=true,
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */
    public function getMetasAction(Request $request, $fileId)
    {
        $fileVersion = $request->get('fileVersion', 1);

        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $fileMetaSetResolver = $this->get('phlexible_media_manager.file_meta_set_resolver');
        $fileMetaDataManager = $this->get('phlexible_media_manager.file_meta_data_manager');

        $file = $volumeManager->getByFileId($fileId)->findFile($fileId, $fileVersion);

        $optionResolver = $this->get('phlexible_meta_set.option_resolver');

        $fileMetaSets = array();

        foreach ($fileMetaSetResolver->resolve($file) as $metaSet) {
            $metaData = $fileMetaDataManager->findByMetaSetAndFile($metaSet, $file);

            $fileMetas = array();

            foreach ($metaSet->getFields() as $field) {
                $options = $optionResolver->resolve($field);

                $fileMeta = array(
                    'key'          => $field->getName(),
                    'type'         => $field->getType(),
                    'options'      => $options,
                    'readonly'     => $field->isReadonly(),
                    'required'     => $field->isRequired(),
                    'synchronized' => $field->isSynchronized(),
                    'values'       => array(),
                    'leaf'         => true,
                );

                if ($metaData) {
                    foreach ($metaData->getLanguages() as $language) {
                        $fileMeta['values'][$language] = $metaData->get($field->getName(), $language);
                    }
                }

                $fileMetas[] = $fileMeta;
            }

            $fileMetaSets[] = array(
                'id'       => $metaSet->getId(),
                'title'    => $metaSet->getName(),
                'children' => $fileMetas
            );
        }

        return array(
            'metas' => $fileMetaSets
        );
    }

    /**
     * @param Request $request
     * @param string  $fileId
     *
     * @return Response
     */
    public function putMetaAction(Request $request, $fileId)
    {
        $fileVersion = $request->get('fileVersion', 1);
        $data = $request->get('data');
        $data = json_decode($data, true);

        $metaLanguages = explode(',', $this->container->getParameter('phlexible_meta_set.languages.available'));

        $metaSetManager = $this->get('phlexible_meta_set.meta_set_manager');
        $fileMetaDataManager = $this->get('phlexible_media_manager.file_meta_data_manager');

        $volume = $this->get('phlexible_media_manager.volume_manager')->getByFileId($fileId);
        $file = $volume->findFile($fileId, $fileVersion);

        /*
        $beforeEvent = new BeforeSaveFileMeta($file);
        if ($dispatcher->dispatch($beforeEvent)->isPropagationStopped()) {
            $this->getResponse()->setAjaxPayload(
                MWF_Ext_Result::encode(false, null, $beforeEvent->getCancelReason())
            );

            return;
        }
        */

        $metaSetIds = $file->getAttribute('metasets', array());

        foreach ($data as $metaSetId => $fields) {
            $metaSet = $metaSetManager->find($metaSetId);
            $metaData = $fileMetaDataManager->findByMetaSetAndFile($metaSet, $file);

            if (!$metaData) {
                $metaData = $fileMetaDataManager->createFileMetaData($metaSet, $file);
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

            $fileMetaDataManager->updateMetaData($metaData);
        }

        /*
        $event = new Media_Manager_Event_SaveFileMeta($file);
        $dispatcher->dispatch($event);
        */

        return new ResultResponse(true, 'File meta saved.');
    }
}
