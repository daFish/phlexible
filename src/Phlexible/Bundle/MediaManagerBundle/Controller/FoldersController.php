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
use Phlexible\Bundle\MediaManagerBundle\Event\GetSlotsEvent;
use Phlexible\Bundle\MediaManagerBundle\MediaManagerEvents;
use Phlexible\Component\MediaManager\Slot\SiteSlot;
use Phlexible\Component\MediaManager\Slot\Slots;
use Phlexible\Component\MediaManager\Volume\ExtendedFolderInterface;
use Phlexible\Component\Volume\Folder\SizeCalculator;
use Phlexible\Component\Volume\Model\FolderInterface;
use Phlexible\Component\Volume\VolumeInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Folder controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_MEDIA')")
 * @Rest\NamePrefix("phlexible_api_mediamanager_")
 */
class FoldersController extends FOSRestController
{
    /**
     * List folders
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a collection of Folder",
     *   section="mediamanager",
     *   resource=true,
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */
    public function getFoldersAction(Request $request)
    {
        $data = array();

        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $permissionRegistry = $this->get('phlexible_access_control.permission_registry');
        $folderSerializer = $this->get('phlexible_media_manager.folder_serializer');

        foreach ($volumeManager->all() as $volume) {
            $rootFolder = $volume->findRootFolder();

            if (
                !$this->isGranted('ROLE_SUPER_ADMIN') &&
                !$this->isGranted('FOLDER_READ', $rootFolder)
            ) {
                continue;
            }

            // TODO: fix
            $userRights = array();
            foreach ($permissionRegistry->get(get_class($rootFolder))->all() as $permission) {
                $userRights[] = $permission->getName();
            }

            $data = $folderSerializer->serialize($rootFolder);
            $data['rights'] = $userRights;
            $data['text'] = $data['name'];
            $data['expanded'] = true;
        }

        return array(
            'folders' => $data
        );
    }

    /**
     * List folders
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a collection of Folder",
     *   section="mediamanager",
     *   resource=true,
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */
    public function getFolderFoldersAction(Request $request, $folderId)
    {
        $data = array();

        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $permissionRegistry = $this->get('phlexible_access_control.permission_registry');
        $folderSerializer = $this->get('phlexible_media_manager.folder_serializer');

        $volume = $volumeManager->getByFolderId($folderId);
        $folder = $volume->findFolder($folderId);

        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('FOLDER_READ', $folder)) {
            return new JsonResponse(array());
        }

        $folders = array();
        foreach ($volume->findFoldersByParentFolder($folder) as $subFolder) {
            if (
                !$this->isGranted('ROLE_SUPER_ADMIN') &&
                !$this->isGranted('FOLDER_READ', $subFolder)
            ) {
                continue;
            }

            // TODO: fix
            $userRights = array();
            foreach ($permissionRegistry->get(get_class($subFolder))->all() as $permission) {
                $userRights[] = $permission->getName();
            }

            $folderUsageService = $this->get('phlexible_media_manager.folder_usage_manager');
            $usage = $folderUsageService->getStatus($subFolder);
            $usedIn = $folderUsageService->getUsedIn($subFolder);
            // TODO: also files in folder!

            $tmp = $folderSerializer->serialize($subFolder);

            $tmp['rights'] = $userRights;
            $tmp['text'] = $tmp['name'];
            $tmp['expanded'] = false;
            $tmp['expandable'] = false;
            $tmp['leaf'] = true;
            if ($volume->countFoldersByParentFolder($subFolder)) {
                $tmp['leaf'] = false;
                $tmp['expandable'] = true;
            }

            $data[] = $tmp;
        }

        return array(
            'folders' => $data
        );
    }

    /**
     * Return folder
     *
     * @param Request $request
     * @param string  $folderId
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a Folder",
     *   section="mediamanager",
     *   output="Phlexible\Component\MediaManager\Model\ExtendedFolderInterface",
     *   statusCodes={
     *     200="Returned when successful",
     *     404="Returned when file was not found"
     *   }
     * )
     */
    public function getFolderAction(Request $request, $folderId)
    {
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $folderSerializer = $this->get('phlexible_media_manager.folder_serializer');

        $volume = $volumeManager->getByFolderId($folderId);
        $folder = $volume->findFolder($folderId);

        $data = $folderSerializer->serialize($folder);

        return array(
            'folder' => $data,
        );
    }

    /**
     * Folder size
     *
     * @param Request $request
     * @param string  $folderId
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a Folder",
     *   section="mediamanager",
     *   output="Phlexible\Component\MediaManager\Model\ExtendedFolderInterface",
     *   statusCodes={
     *     200="Returned when successful",
     *     404="Returned when folder was not found"
     *   }
     * )
     */
    public function getFolderSizeAction(Request $request, $folderId)
    {
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $volume = $volumeManager->getByFolderId($folderId);

        $folder = $volume->findFolder($folderId);

        if (!$folder instanceof FolderInterface) {
            throw new NotFoundHttpException("Folder not found");
        }

        $calculator = new SizeCalculator();
        $calculatedSize = $calculator->calculate($volume, $folder);

        $data = array(
            'title'       => $folder->getName(),
            'type'        => 'folder',
            'path'        => '/' . $folder->getPath(),
            'size'        => $calculatedSize->getSize(),
            'files'       => $calculatedSize->getNumFiles(),
            'folders'     => $calculatedSize->getNumFolders(),
            'create_time' => $folder->getCreatedAt()->format('U') * 1000,
            'create_user' => $folder->getCreateUser(),
            'modify_time' => $folder->getModifiedAt()->format('U') * 1000,
            'modify_user' => $folder->getModifyUser(),
        );

        return array(
            'size' => $data
        );
    }

    /**
     * Create new folder
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Rest\View(statusCode=201)
     * @ApiDoc(
     *   description="Create a Folder",
     *   section="mediamanager",
     *   statusCodes={
     *     201="Returned when successful",
     *     404="Returned when folder was not found"
     *   }
     * )
     */
    public function postFoldersAction(Request $request)
    {
        $name = $request->get('name');
        $parentId = $request->get('parentId');

        $volume = $this->getVolumeByFolderId($parentId);
        $parentFolder = $volume->findFolder($parentId);

        $parentFolder->getVolume()
            ->createFolder($parentFolder, $name, array(), $this->getUser()->getUsername());
    }

    /**
     * Update folder
     *
     * @param Request $request
     * @param string  $folderId
     *
     * @return Response
     *
     * @Rest\View(statusCode=204)
     * @ApiDoc(
     *   description="Update a Folder",
     *   section="mediamanager",
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when folder was not found"
     *   }
     * )
     */
    public function putFolderAction(Request $request, $folderId)
    {
        $name = $request->get('name', false);
        $parentId = $request->get('targetId', false);

        $volume = $this->getVolumeByFolderId($folderId);
        $folder = $volume->findFolder($folderId);

        if (!$folder instanceof FolderInterface) {
            throw new NotFoundHttpException("Folder not found");
        }

        if ($name) {
            $volume->renameFolder($folder, $name, $this->getUser()->getId());
        }

        if ($parentId) {
            $targetFolder = $volume->findFolder($parentId);
            $volume->moveFolder($folder, $targetFolder, $this->getUser()->getUsername());
        }
    }

    /**
     * Rename folder
     *
     * @param Request $request
     * @param string  $folderId
     *
     * @return Response
     * @deprecated
     *
     * @Rest\View(statusCode=204)
     * @ApiDoc(
     *   description="Rename a Folder",
     *   section="mediamanager",
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when folder was not found"
     *   }
     * )
     */
    public function renameFolderAction(Request $request, $folderId)
    {
        $folderName = $request->get('name');

        $volume = $this->getVolumeByFolderId($folderId);
        $folder = $volume->findFolder($folderId);

        if (!$folder instanceof FolderInterface) {
            throw new NotFoundHttpException("Folder not found");
        }

        $volume->renameFolder($folder, $folderName, $this->getUser()->getUsername());
    }

    /**
     * Move folder
     *
     * @param Request $request
     *
     * @return Response
     * @deprecated
     *
     * @Rest\View(statusCode=204)
     * @ApiDoc(
     *   description="Move a Folder",
     *   section="mediamanager",
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when folder was not found"
     *   }
     * )
     */
    public function moveFolderAction(Request $request, $folderId)
    {
        $volumeId = $request->get('volumeId');
        $targetId = $request->get('targetId');

        $volume = $this->getVolume($volumeId);
        $folder = $volume->findFolder($folderId);

        if (!$folder instanceof FolderInterface) {
            throw new NotFoundHttpException("Folder not found");
        }

        $targetFolder = $volume->findFolder($targetId);

        $volume->moveFolder($folder, $targetFolder, $this->getUser()->getUsername());

        return new ResultResponse(true);
    }

    /**
     * Delete folder
     *
     * @param Request $request
     * @param string  $folderId
     *
     * @return Response
     * @deprecated
     *
     * @Rest\View(statusCode=204)
     * @ApiDoc(
     *   description="Move a Folder",
     *   section="mediamanager",
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when folder was not found"
     *   }
     * )
     */
    public function deleteFolderAction(Request $request, $folderId)
    {
        $volume = $this->getVolumeByFolderId($folderId);
        $folder = $volume->findFolder($folderId);

        if (!$folder instanceof FolderInterface) {
            throw new NotFoundHttpException("Folder not found");
        }

        if ($folder->isRoot()) {
            return new BadRequestHttpException("Can't delete the root folder.");
        }

        $volume->deleteFolder($folder, $this->getUser()->getUsername());
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

    /**
     * @param string $volumeId
     *
     * @return VolumeInterface
     */
    private function getVolume($volumeId = null)
    {
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');

        if ($volumeId) {
            return $volumeManager->getById($volumeId);
        }

        return current($volumeManager->all());
    }

    /**
     * @param ExtendedFolderInterface $folder
     *
     * @return array
     */
    private function recurseFolders(ExtendedFolderInterface $folder)
    {
        $volume = $folder->getVolume();
        $subFolders = $volume->findFoldersByParentFolder($folder);

        $permissionRegistry = $this->get('phlexible_access_control.permission_registry');

        $user = $this->getUser();

        $children = array();
        foreach ($subFolders as $subFolder) {
            /* @var $subFolder ExtendedFolderInterface */

            if (!$this->isGranted('FOLDER_READ', $folder)) {
                continue;
            }

            // TODO: fox
            $userRights = array();
            foreach ($permissionRegistry->get(get_class($subFolder))->all() as $permission) {
                $userRights[] = $permission->getName();
            }

            $tmp = array(
                'id'        => $subFolder->getId(),
                'text'      => $subFolder->getName(),
                'leaf'      => false,
                'numChilds' => $volume->countFilesByFolder($subFolder),
                'draggable' => true,
                'expanded'  => true,
                'allowDrop' => true,
                'allowChildren' => true,
                'isTarget'  => true,
                'rights'    => $userRights,
            );

            if ($volume->countFoldersByParentFolder($subFolder)) {
                $tmp['children'] = $this->recurseFolders($subFolder);
                $tmp['expanded'] = false;
            } else {
                $tmp['children'] = array();
                $tmp['expanded'] = true;
            }

            $children[] = $tmp;
        }

        return $children;
    }
}
