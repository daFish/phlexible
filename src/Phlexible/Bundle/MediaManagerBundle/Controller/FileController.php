<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Component\Volume\Exception\AlreadyExistsException;
use Phlexible\Component\Volume\Exception\NotFoundException;
use Phlexible\Component\Volume\VolumeInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * File controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediamanager/file")
 * @Security("is_granted('ROLE_MEDIA')")
 */
class FileController extends Controller
{
    /**
     * List files
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="mediamanager_file_list")
     * @Method("GET")
     */
    public function listAction(Request $request)
    {
        $folderId = $request->get('folderId');
        $start = $request->get('start', 0);
        $limit = $request->get('limit', 25);
        $sort = $request->get('sort', 'name');
        $dir = $request->get('dir', 'ASC');
        $showHidden = $request->get('show_hidden', false);
        $filter = $request->get('filter');

        if (!$folderId) {
            throw new \RuntimeException("No folder ID");
        }

        if ($filter) {
            $filter = json_decode($filter, true);
        }

        $data = [];
        $total = 0;

        $volume = $this->getVolumeByFolderId($folderId);
        $securityContext = $this->get('security.context');

        $folder = $volume->findFolder($folderId);

        if ($securityContext->isGranted('ROLE_SUPER_ADMIN') || $securityContext->isGranted('FILE_READ', $folder)) {
            if ($sort === 'create_time') {
                $sort = 'created_at';
            } elseif ($sort === 'document_type_key') {
                $sort = 'mime_type';
            }

            if ($filter) {
                $filter['folder'] = $folder;
                if (!$showHidden) {
                    $filter['hidden'] = false;
                }
                if (!empty($filter['assetType'])) {
                    $filter['mediaCategory'] = $filter['assetType'];
                    unset($filter['assetType']);
                }
                if (!empty($filter['documenttypeType'])) {
                    $filter['mediaType'] = $filter['documenttypeType'];
                    unset($filter['documenttypeType']);
                }
                $files = $volume->findFiles($filter, [$sort => $dir], $limit, $start);
                $total = $volume->countFiles($filter);
            } else {
                $files = $volume->findFilesByFolder($folder, [$sort => $dir], $limit, $start, $showHidden);
                $total = $volume->countFilesByFolder($folder, $showHidden);
            }

            $serializer = $this->get('phlexible_media_manager.file_serializer');

            $data = [];
            foreach ($files as $file) {
                $data[] = $serializer->serialize($file, $request->getLocale());
            }
        }

        return new JsonResponse(['files' => $data, 'total' => $total]);
    }

    /**
     * Properties
     *
     * @param Request $request
     * @param string  $fileId
     *
     * @return JsonResponse
     * @Route("/{fileId}", name="mediamanager_file_detail")
     * @Method("GET")
     */
    public function detailsAction(Request $request, $fileId)
    {
        $fileVersion = $request->get('fileVersion', 1);

        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $serializer = $this->get('phlexible_media_manager.file_serializer');

        $volume = $volumeManager->getByFileId($fileId);
        $file = $volume->findFile($fileId, $fileVersion);

        $data = $serializer->serialize($file, $request->getLocale());

        return new JsonResponse($data);
    }

    /**
     * Delete File
     *
     * @param Request $request
     * @param string  $fileId
     *
     * @return ResultResponse
     * @Route("/{fileId}", name="mediamanager_file_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $fileId)
    {
        $fileIds = explode(',', $fileId);

        $volumeManager = $this->get('phlexible_media_manager.volume_manager');

        foreach ($fileIds as $fileId) {
            try {
                $volume = $volumeManager->getByFileId($fileId);
                $file = $volume->findFile($fileId);
                if ($file) {
                    $volume->deleteFile($file, $this->getUser()->getId());
                }
            } catch (NotFoundException $e) {
            }
        }

        return new ResultResponse(true, count($fileIds) . ' file(s) deleted.');
    }

    /**
     * Patch file
     *
     * @param Request $request
     * @param string  $fileId
     *
     * @return ResultResponse
     * @Route("/file/{fileId}", name="mediamanager_file_patch")
     * @Method("PATCH")
     */
    public function patchAction(Request $request, $fileId)
    {
        $name = $request->get('name', false);
        $hide = $request->get('hide', false);
        $show = $request->get('show', false);
        $folderId = $request->get('folderId', false);

        $volume = $this->get('phlexible_media_manager.volume_manager')->getByFileId($fileId);

        $file = $volume->findFile($fileId);

        if ($name) {
            $volume->renameFile($file, $name, $this->getUser()->getId());
        }
        if ($hide) {
            $volume->hideFile($file, $this->getUser()->getId());
        }
        if ($show) {
            $volume->showFile($file, $this->getUser()->getId());
        }
        if ($folderId) {
            $folder = $volume->findFolder($folderId);
            $volume->moveFile($file, $folder, $this->getUser()->getId());
        }

        return new ResultResponse(true, 'File patched.');
    }

    /**
     * Hide File
     *
     * @param Request $request
     * @param string  $fileId
     *
     * @return ResultResponse
     * @Route("/hide/{fileId}", name="mediamanager_file_hide")
     * @Method("PUT")
     * @deprecated
     */
    public function hideAction(Request $request, $fileId)
    {
        $fileId = $request->get('fileId');
        $fileIds = explode(',', $fileId);

        $volumeManager = $this->get('phlexible_media_manager.volume_manager');

        foreach ($fileIds as $fileId) {
            $volume = $volumeManager->getByFileId($fileId);
            $file = $volume->findFile($fileId);
            $volume->hide();
        }

        return new ResultResponse(true, count($fileIds) . ' file(s) hidden.');
    }

    /**
     * Show file
     *
     * @param Request $request
     * @param string  $fileId
     *
     * @return JsonResponse
     * @Route("/show/{fileId}", name="mediamanager_file_show")
     * @Method("PUT")
     * @deprecated
     */
    public function showAction(Request $request, $fileId)
    {
        $fileIds = explode(',', $fileId);

        $volumeManager = $this->get('phlexible_media_manager.volume_manager');

        foreach ($fileIds as $fileId) {
            $volume = $volumeManager->getByFileId($fileId);
            $file = $volume->findFile($fileId);
            $volume->hide($file);
        }

        return new ResultResponse(true, count($fileIds) . ' file(s) shown.');
    }

    /**
     * Rename file
     *
     * @param Request $request
     * @param string  $fileId
     *
     * @return ResultResponse
     * @Route("/rename/{fileId}", name="mediamanager_file_rename")
     * @Method("PUT")
     * @deprecated
     */
    public function renameAction(Request $request, $fileId)
    {
        $name = $request->get('file_name');

        $volume = $this->get('phlexible_media_manager.volume_manager')->getByFileId($fileId);

        $file = $volume->findFile($fileId);
        $volume->renameFile($file, $name, $this->getUser()->getId());

        return new ResultResponse(true, 'File(s) renamed.');
    }

    /**
     * Copy file
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/copy", name="mediamanager_file_copy")
     * @Method("POST")
     */
    public function copyAction(Request $request)
    {
        $folderId = $request->get('folderId');
        $fileIDs = json_decode($request->get('fileIds'));

        $volume = $this->getVolumeByFolderId($folderId);
        $folder = $volume->findFolder($folderId);

        foreach ($fileIDs as $fileID) {
            $file = $volume->findFile($fileID);
            $volume->copyFile($file, $folder, $this->getUser()->getId());
        }

        return new ResultResponse(true, 'File(s) copied.');
    }

    /**
     * Move file
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/move", name="mediamanager_file_move")
     * @deprecated
     */
    public function moveAction(Request $request)
    {
        $folderId = $request->get('folderId');
        $fileIds = explode(',', $request->get('fileId'));

        $volume = $this->getVolumeByFolderId($folderId);
        $folder = $volume->findFolder($folderId);

        $skippedFiles = [];

        foreach ($fileIds as $fileId) {
            $file = $volume->findFile($fileId);
            try {
                $volume->moveFile($file, $folder, $this->getUser()->getId());
            } catch (AlreadyExistsException $e) {
                $skippedFiles[] = $file->getName();
            }
        }

        return new ResultResponse(true, 'File(s) moved.', array('skipped' => $skippedFiles));
    }

    /**
     * @param string $folderId
     *
     * @return VolumeInterface
     */
    private function getVolumeByFolderId($folderId)
    {
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');

        return $volumeManager->getByFolderId($folderId);
    }
}
