<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\MediaManagerBundle\Event\GetSlotsEvent;
use Phlexible\Bundle\MediaManagerBundle\MediaManagerEvents;
use Phlexible\Component\MediaManager\Slot\SiteSlot;
use Phlexible\Component\MediaManager\Slot\Slots;
use Phlexible\Component\MediaManager\Volume\ExtendedFolderInterface;
use Phlexible\Component\Volume\Exception\AlreadyExistsException;
use Phlexible\Component\Volume\Folder\SizeCalculator;
use Phlexible\Component\Volume\VolumeInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Folder controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediamanager/folder")
 * @Security("is_granted('ROLE_MEDIA')")
 */
class FolderController extends Controller
{
    /**
     * List folders
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="mediamanager_folder_list")
     * @Method("GET")
     */
    public function listAction(Request $request)
    {
        $folderId = $request->get('node', null);

        $data = [];

        $slots = new Slots();
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $dispatcher = $this->get('event_dispatcher');
        $securityContext = $this->get('security.context');
        $permissions = $this->get('phlexible_access_control.permissions');
        $folderSerializer = $this->get('phlexible_media_manager.folder_serializer');

        $user = $this->getUser();

        if (!$folderId || $folderId === 'root') {
            foreach ($volumeManager->all() as $volume) {
                $rootFolder = $volume->findRootFolder();

                if (!$securityContext->isGranted('ROLE_SUPER_ADMIN') && !$securityContext->isGranted('FOLDER_READ', $rootFolder)) {
                    continue;
                }

                // TODO: rights
                /*
                $userRights = $rootFolder->getRights(MWF_Env::getUser());
                if (null === $userRights)
                {
                    continue;
                }
                $userRights = array('FOLDER_READ', 'FOLDER_CREATE', 'FOLDER_MODIFY', 'FOLDER_DELETE', 'FOLDER_RIGHTS', 'FILE_READ', 'FILE_CREATE', 'FILE_MODIFY', 'FILE_DELETE', 'FILE_DOWNLOAD');
                */
                $userRights = array_keys($permissions->getByContentClass(get_class($rootFolder)));

                $data = $folderSerializer->serialize($rootFolder, $request->getLocale());
                $data['rights'] = $userRights;
                $data['text'] = $data['name'];
                $data['expanded'] = true;

                $slot = new SiteSlot();
                $slot->setData(
                    [
                        $data
                    ]
                );

                $slots->append($slot);
            }

            $event = new GetSlotsEvent($slots);
            $dispatcher->dispatch(MediaManagerEvents::GET_SLOTS, $event);

            $data = $slots->getAllData();

            //            $data[] = array(
            //                'id'        => 'tags',
            //                'text'      => 'Tags',
            //                'iconCls'   => 'p-mediamanager-tag-icon',
            //                'cls'       => 't-mediamanager-root',
            //                'leaf'      => false,
            //                'children' => array(array(
            //                    'id' => 'tag1',
            //                    'text' => 'tag1',
            //                    'leaf' => true
            //                )),
            //                'draggable' => false,
            //                'expanded'  => true,
            //                'allowDrag' => false,
            //                'allowDrop' => false,
            //                'module'    => true,
            //            );
        } else {
            $slotKey = $request->get('slot', null);
            if (!$slotKey) {
                $volume = $volumeManager->getByFolderId($folderId);
                $folder = $volume->findFolder($folderId);

                if (!$securityContext->isGranted('ROLE_SUPER_ADMIN') && !$securityContext->isGranted('FOLDER_READ', $rootFolder)) {
                    return new JsonResponse([]);
                }

                foreach ($volume->findFoldersByParentFolder($folder) as $subFolder) {
                    if (!$securityContext->isGranted('ROLE_SUPER_ADMIN') && !$securityContext->isGranted('FOLDER_READ', $rootFolder)) {
                        continue;
                    }

                    /*
                    $userRights = $subFolder->getRights(MWF_Env::getUser());
                    if (null === $userRights)
                    {
                        continue;
                    }
                    $userRights = array();
                    */
                    $userRights = array_keys($permissions->getByContentClass(get_class($subFolder)));;

                    $folderUsageService = $this->get('phlexible_media_manager.folder_usage_manager');
                    $usage = $folderUsageService->getStatus($folder);
                    $usedIn = $folderUsageService->getUsedIn($folder);
                    // TODO: also files in folder!

                    $tmp = $folderSerializer->serialize($subFolder, $request->getLocale());

                    $tmp['rights'] = $userRights;
                    $tmp['text'] = $tmp['name'];
                    $tmp['expanded'] = false;
                    $tmp['expandable'] = false;
                    if ($volume->countFoldersByParentFolder($subFolder)) {
                        //$tmp['leaf'] = true;
                        $tmp['expandable'] = true;
                        //$tmp['children'] = [];
                    }

                    $data[] = $tmp;
                }
            } else {
                $slots = new Slots();

                $event = new GetSlotsEvent($slots);
                $dispatcher->dispatch($event);

                $slot = $slots->getSlot($slotKey);

                $data = $slot->getData(false);
            }
        }

        return new JsonResponse($data);
    }

    /**
     * Folder size
     *
     * @param Request $request
     * @param string  $folderId
     *
     * @return JsonResponse
     * @Route("/{folderId}/size", name="mediamanager_folder_size")
     * @Method("GET")
     */
    public function sizeAction(Request $request, $folderId)
    {
        $folderId = $request->get('folderId');
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $volume = $volumeManager->getByFolderId($folderId);

        try {
            $folder = $volume->findFolder($folderId);

            $calculator = new SizeCalculator();
            $calculatedSize = $calculator->calculate($volume, $folder);

            $data = [
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
            ];
        } catch (\Exception $e) {
            $data = [];
        }

        return new JsonResponse($data);
    }

    /**
     * Create new folder
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("", name="mediamanager_folder_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $name = $request->get('name');
        $parentId = $request->get('parentId');

        $volume = $this->getVolumeByFolderId($parentId);
        $parentFolder = $volume->findFolder($parentId);

        try {
            $folder = $parentFolder->getVolume()
                ->createFolder($parentFolder, $name, array(), $this->getUser()->getId());

            return new ResultResponse(true, 'Folder created.', [
                'folderId'   => $folder->getId(),
                'folderName' => $folder->getName()
            ]);
        } catch (AlreadyExistsException $e) {
            return new ResultResponse(false, $e->getMessage(), [
                'name' => 'Folder already exists.'
            ]);
        }
    }

    /**
     * Path folder
     *
     * @param Request $request
     * @param string  $folderId
     *
     * @return ResultResponse
     * @Route("/{folderId}", name="mediamanager_folder_patch")
     * @Method("PATCH")
     */
    public function patchAction(Request $request, $folderId)
    {
        $name = $request->get('name', false);
        $parentId = $request->get('targetId', false);

        $volume = $this->getVolumeByFolderId($folderId);
        $folder = $volume->findFolder($folderId);

        if ($name) {
            $volume->renameFolder($folder, $name, $this->getUser()->getId());
        }

        if ($parentId) {
            $targetFolder = $volume->findFolder($parentId);
            $volume->moveFolder($folder, $targetFolder, $this->getUser()->getId());
        }

        return new ResultResponse(true, 'Folder patched.', [
            'name' => $folder->getName(),
        ]);
    }

    /**
     * Rename folder
     *
     * @param Request $request
     * @param string  $folderId
     *
     * @return ResultResponse
     * @Route("/rename/{folderId}", name="mediamanager_folder_rename")
     * @Method("PUT")
     */
    public function renameAction(Request $request, $folderId)
    {
        $folderName = $request->get('name');

        $volume = $this->getVolumeByFolderId($folderId);
        $folder = $volume->findFolder($folderId);

        $volume->renameFolder($folder, $folderName, $this->getUser()->getId());

        return new ResultResponse(true, 'Folder renamed.', [
            'folderName' => $folderName
        ]);
    }

    /**
     * Move folder
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/move", name="mediamanager_folder_move")
     * @Method("PUT")
     */
    public function moveAction(Request $request)
    {
        $volumeId = $request->get('volumeId');
        $targetId = $request->get('targetId');
        $sourceId = $request->get('id');

        $volume = $this->getVolume($volumeId);
        $folder = $volume->findFolder($sourceId);
        $targetFolder = $volume->findFolder($targetId);

        $volume->moveFolder($folder, $targetFolder, $this->getUser()->getId());

        return new ResultResponse(true);
    }

    /**
     * Delete folder
     *
     * @param Request $request
     * @param string  $folderId
     *
     * @return ResultResponse
     * @Route("/delete", name="mediamanager_folder_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $folderId)
    {
        $volume = $this->getVolumeByFolderId($folderId);
        $folder = $volume->findFolder($folderId);

        if ($folder->isRoot()) {
            return new ResultResponse(false, "Can't delete the root folder.");
        }

        $volume->deleteFolder($folder, $this->getUser()->getId());

        return new ResultResponse(true, 'Folder deleted', ['parent_id' => $folder->getParentId()]);
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

        $securityContext = $this->get('security.context');
        $permissions = $this->get('phlexible_access_control.permissions');

        $user = $this->getUser();

        $children = [];
        foreach ($subFolders as $subFolder) {
            /* @var $subFolder ExtendedFolderInterface */

            if (!$securityContext->isGranted('FOLDER_READ', $folder)) {
                continue;
            }

            // TODO: rights
            /*
            $userRights = $subFolder->getRights(MWF_Env::getUser());
            if (null === $userRights) {
                continue;
            }
            $userRights = array('FOLDER_READ', 'FOLDER_CREATE', 'FOLDER_MODIFY', 'FOLDER_DELETE', 'FOLDER_RIGHTS', 'FILE_READ', 'FILE_CREATE', 'FILE_MODIFY', 'FILE_DELETE', 'FILE_DOWNLOAD');
            */
            $userRights = array_keys($permissions->get(get_class($subFolder), get_class($user)));

            $tmp = [
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
            ];

            if ($volume->countFoldersByParentFolder($subFolder)) {
                $tmp['children'] = $this->recurseFolders($subFolder);
                $tmp['expanded'] = false;
            } else {
                $tmp['children'] = [];
                $tmp['expanded'] = true;
            }

            $children[] = $tmp;
        }

        return $children;
    }
}
