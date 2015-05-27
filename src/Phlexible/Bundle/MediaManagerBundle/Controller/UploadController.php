<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Controller;

use Brainbits\Mime\MimeDetector;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\MediaManagerBundle\Entity\File;
use Phlexible\Bundle\MediaManagerBundle\MediaManagerMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Upload controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediamanager/upload")
 * @Security("is_granted('ROLE_MEDIA')")
 */
class UploadController extends Controller
{
    /**
     * Upload File
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("", name="mediamanager_upload")
     */
    public function uploadAction(Request $request)
    {
        $folderId = $request->get('folder_id', null);

        try {
            $volumeManager = $this->get('phlexible_media_manager.volume_manager');
            $volume = $volumeManager->getByFolderId($folderId);
            $folder = $volume->findFolder($folderId);

            if (empty($folder)) {
                return new ResultResponse(
                    false,
                    'Target folder not found.',
                    [
                        'params' => $request->request->all(),
                        'files'  => $request->files->all(),
                    ]
                );
            }

            if (!$request->files->count()) {
                return new ResultResponse(
                    false,
                    'No files received.',
                    [
                        'params' => $request->request->all(),
                        'files'  => $request->files->all(),
                    ]
                );
            }

            $uploadHandler = $this->get('phlexible_media_manager.upload.handler');

            $cnt = 0;
            foreach ($request->files->all() as $uploadedFile) {
                /* @var $uploadedFile UploadedFile */

                $file = $uploadHandler->handle($uploadedFile, $folderId, $this->getUser()->getId());

                if ($file) {
                    $cnt++;

                    $body = 'Filename: ' . $uploadedFile->getClientOriginalName() . PHP_EOL
                        . 'Folder:   ' . $folder->getName() . PHP_EOL
                        . 'Filesize: ' . $uploadedFile->getSize() . PHP_EOL
                        . 'Filetype: ' . $file->getMimeType() . PHP_EOL;

                    $message = MediaManagerMessage::create('File "' . $file->getName() . '" uploaded.', $body);
                    $this->get('phlexible_message.message_poster')->post($message);
                }
            }

            return new ResultResponse(
                true,
                $cnt . ' file(s) uploaded.',
                [
                    'params' => $request->request->all(),
                    'files'  => $request->files->all(),
                ]
            );
        } catch (\Exception $e) {
            return new ResultResponse(
                false,
                $e->getMessage(),
                [
                    'params' => $request->request->all(),
                    'files'  => $request->files->all(),
                    'trace'  => $e->getTraceAsString(),
                    'traceArray'  => $e->getTrace(),
                ]
            );
        }
    }

    /**
     * @return JsonResponse
     * @Route("/check", name="mediamanager_upload_check")
     */
    public function checkAction()
    {
        $tempHandler = $this->get('phlexible_media_manager.upload.temp_handler');
        $tempStorage = $this->get('phlexible_media_manager.upload.temp_storage');
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $mediaClassifier = $this->get('phlexible_media.media_classifier');

        $data = [];

        if ($tempStorage->count()) {
            $tempFile = $tempStorage->next();
            $volume = $volumeManager->getByFolderId($tempFile->getFolderId());
            $supportsVersions = $volume->hasFeature('versions');
            $newName = basename($tempFile->getName());
            $file = new \Symfony\Component\HttpFoundation\File\File($tempFile->getPath());
            $mimetype = $file->getMimeType();
            if ($mimetype) {
                $newType = $mediaClassifier->getCollection()->lookup($mimetype);
            } else {
                $newType = $mediaClassifier->getCollection()->lookup('binary');
            }

            $data = [
                'versions' => $supportsVersions,
                'temp_key' => $tempFile->getId(),
                'temp_id'  => $tempFile->getId(),
                'new_id'   => $tempFile->getFileId(),
                'new_name' => $newName,
                'new_type' => $newType->getName(),
                'new_size' => $tempFile->getSize(),
                'wizard'   => false,
                'total'    => $tempStorage->count(),
            ];

            if ($tempFile->getFileId()) {
                $oldFile = $volume->findFile($tempFile->getFileId());

                $alternativeName = $tempHandler->createAlternateFilename($tempFile, $volume);

                $data['old_name'] = $tempFile->getName();
                $data['old_id']   = $tempFile->getFileId();
                $data['old_type'] = $oldFile->getMediaType();
                $data['old_size'] = $oldFile->getSize();
                $data['alternative_name'] = $alternativeName;
            }

            if ($tempFile->getFileId()) {
                $data['file_id'] = $tempFile->getFileId();
            }

            if ($tempFile->getUseWizard()) {
                $data['wizard'] = true;
            }

            /*
            // TODO: parser stuff
            if (!empty($tempFile['parsed'])) {
                $temp['parsed'] = $tempFile['parsed'];
            }
            */
        }

        return new JsonResponse($data);
    }

    /**
     * @return JsonResponse
     * @Route("/clear", name="mediamanager_upload_clear")
     */
    public function clearAction()
    {
        $tempStorage = $this->get('phlexible_media_manager.upload.temp_storage');
        $tempStorage->removeAll();

        return new ResultResponse(true);
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/save", name="mediamanager_upload_save")
     */
    public function saveAction(Request $request)
    {
        $all = $request->get('all');
        $action = $request->get('do');
        $tempId = $request->get('temp_id');

        $metaSetId = $request->get('metaset', null);
        $metaData = $request->get('meta', null);
        if ($metaData) {
            $metaData = json_decode($metaData, true);
        }

        $tempHandler = $this->get('phlexible_media_manager.upload.temp_handler');

        if ($all) {
            $tempHandler->handleAll($action);
        } else {
            $tempHandler->handle($action, $tempId);
        }

        return new ResultResponse(true, ($all ? 'All' : 'File') . ' saved with action ' . $action);
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/preview", name="mediamanager_upload_preview")
     */
    public function previewAction(Request $request)
    {
        $tempId = $request->get('id');
        $templateKey = $request->get('template');

        $tempStorage = $this->get('phlexible_media_manager.upload.temp_storage');
        $templateManager = $this->get('phlexible_media_template.template_manager');
        $imageApplier = $this->get('phlexible_media_template.applier.image');

        $template = $templateManager->find($templateKey);
        $tempFile = $tempStorage->get($tempId);

        $outFilename = $this->container->getParameter('phlexible_media_manager.temp_dir') . 'preview.png';
        $imageApplier->apply($template, new File(), $tempFile->getPath(), $outFilename);

        return $this->get('igorw_file_serve.response_factory')
            ->create($outFilename, 'image/png', [
                'absolute_path' => true,
            ]);
    }

    /**
     * @return JsonResponse
     * @Route("/metasets", name="mediamanager_upload_metasets")
     */
    public function metasetsAction()
    {
        $allSets = $this->getContainer()->get('metasets.repository')->getAll();

        $sets = [];
        foreach ($allSets as $key => $set) {
            $sets[] = [
                'key' => $key,
                'title' => $set->getTitle()
            ];
        }

        return new JsonResponse(['metasets' => $sets]);
    }

    /**
     * @return JsonResponse
     * @Route("/meta", name="mediamanager_upload_meta")
     */
    public function metaAction()
    {
        $additionalMetaSet = $this->_getParam('metaset', null);

        $metaSet = $this->getContainer()->get('metasets.repository')->find('document');
        $keys = $metaSet->getKeys();

        $meta = [];

        $t9n = $this->getContainer()->t9n;
        $pageKeys = $t9n->{'metadata-keys'}->toArray();
        $pageSelect = $t9n->{'metadata-selectvalues'}->toArray();

        $container = $this->getContainer();
        $dataSourceRepository = $container->get('dataSourcesRepository');

        foreach ($keys as $key => $row) {
            $meta[$key] = $row;
            $meta[$key]['set_id'] = $metaSet->getId();
            $meta[$key]['key'] = $key;
            $meta[$key]['value_de'] = '';
            $meta[$key]['value_en'] = '';
            $meta[$key]['required'] = (int) $meta[$key]['required'];

            $meta[$key]['tkey'] = $key;
            if (!empty($pageKeys[$key])) {
                $meta[$key]['tkey'] = $pageKeys[$key];
            }

            if ($row['type'] == 'select') {
                $options = explode(',', $row['options']);

                foreach ($options as $k => $okey) {
                    $okey = trim($okey);
                    $value = $okey;
                    if (!empty($pageSelect[$okey])) {
                        $value = $pageSelect[$okey];
                    }
                    $options[$k] = [$okey, $value];
                }

                $meta[$key]['options'] = $options;
            } elseif ($row['type'] == 'suggest') {
                $sourceId = $row['options'];
                $options = ['source_id' => $sourceId];

                foreach (['de', 'en'] as $language) {
                    $source = $dataSourceRepository->getDataSourceById(
                        $sourceId,
                        $language
                    );

                    $keys = $source->getKeys();

                    foreach ($keys as $value) {
                        $options["values_$language"][] = [$value, $value];
                    }

                }

                $meta[$key]['options'] = $options;
            }
        }

        if ($additionalMetaSet) {
            try {
                $metaSet = $this->getContainer()->get('metasets.repository')->find($additionalMetaSet);
                $keys = $metaSet->getKeys();

                foreach ($keys as $key => $row) {
                    $meta[$key] = $row;
                    $meta[$key]['set_id'] = $metaSet->getId();
                    $meta[$key]['key'] = $key;
                    $meta[$key]['value_de'] = '';
                    $meta[$key]['value_en'] = '';
                    $meta[$key]['required'] = (int) $meta[$key]['required'];

                    $meta[$key]['tkey'] = $key;
                    if (!empty($pageKeys[$key])) {
                        $meta[$key]['tkey'] = $pageKeys[$key];
                    }

                    if ($row['type'] == 'select') {
                        $options = explode(',', $row['options']);

                        foreach ($options as $k => $okey) {
                            $okey = trim($okey);
                            $value = $okey;
                            if (!empty($pageSelect[$okey])) {
                                $value = $pageSelect[$okey];
                            }
                            $options[$k] = [$okey, $value];
                        }

                        $meta[$key]['options'] = $options;
                    } elseif ($row['type'] == 'suggest') {
                        $sourceId = $row['options'];
                        $options = ['source_id' => $sourceId];

                        foreach (['de', 'en'] as $language) {
                            $source = $dataSourceRepository->getDataSourceById(
                                $sourceId,
                                $language
                            );

                            $keys = $source->getKeys();

                            foreach ($keys as $value) {
                                $options["values_$language"][] = [$value, $value];
                            }
                        }

                        $meta[$key]['options'] = $options;
                    }
                }
            } catch (\Exception $e) {

            }
        }

        $meta = array_values($meta);

        return new JsonResponse($meta);
    }
}
