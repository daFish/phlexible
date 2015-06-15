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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Folder meta setscontroller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_MEDIA')")
 * @Rest\NamePrefix("phlexible_api_mediamanager_")
 */
class FoldersMetaSetsController extends FOSRestController
{
    /**
     * @param Request $request
     * @param string  $folderId
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a collection of FolderMetaSet",
     *   section="mediamanager",
     *   resource=true,
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */
    public function getMetasetsAction(Request $request, $folderId)
    {
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $folderMetaSetResolver = $this->get('phlexible_media_manager.folder_meta_set_resolver');

        $folder = $volumeManager->getByFolderId($folderId)->findFolder($folderId);
        $metaSets = $folderMetaSetResolver->resolve($folder);

        $sets = [];
        foreach ($metaSets as $metaSet) {
            $sets[] = [
                'id'   => $metaSet->getId(),
                'name' => $metaSet->getName(),
            ];
        }

        return new JsonResponse(['sets' => $sets]);
    }

    /**
     * @param Request $request
     * @param string  $folderId
     *
     * @return Response
     */
    public function putMetasetAction(Request $request, $folderId)
    {
        $joinedIds = $request->get('ids');
        if ($joinedIds) {
            $ids = explode(',', $joinedIds);
        } else {
            $ids = [];
        }

        $volumeManager = $this->get('phlexible_media_manager.volume_manager');

        $volume = $volumeManager->getByFolderId($folderId);
        $folder = $volume->findFolder($folderId);

        $attributes = $folder->getAttributes();
        $attributes->set('metasets', $ids);
        $volume->setFolderAttributes($folder, $attributes, $this->getUser()->getId());

        return new ResultResponse(true, 'Set added.');
    }
}
