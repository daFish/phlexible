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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * File meta sets controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_MEDIA')")
 * @Rest\NamePrefix("phlexible_api_mediamanager_")
 */
class FilesMetaSetsController extends FOSRestController
{
    /**
     * @param Request $request
     * @param string  $fileId
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a collection of FileMetaSet",
     *   section="mediamanager",
     *   resource=true,
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */
    public function getMetasetsAction(Request $request, $fileId)
    {
        $fileVersion = $request->get('fileVersion', 1);

        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $fileMetaSetResolver = $this->get('phlexible_media_manager.file_meta_set_resolver');

        $file = $volumeManager->getByFileId($fileId)->findFile($fileId, $fileVersion);
        $metaSets = $fileMetaSetResolver->resolve($file);

        $sets = array();
        foreach ($metaSets as $metaSet) {
            $sets[] = array(
                'id'   => $metaSet->getId(),
                'name' => $metaSet->getName(),
            );
        }

        return array(
            'sets' => $sets
        );
    }

    /**
     * @param Request $request
     * @param string  $fileId
     *
     * @return Response
     */
    public function putMetasetAction(Request $request, $fileId)
    {
        $fileVersion = $request->get('fileVersion', 1);
        $joinedIds = $request->get('ids');
        if ($joinedIds) {
            $ids = explode(',', $joinedIds);
        } else {
            $ids = array();
        }

        $volumeManager = $this->get('phlexible_media_manager.volume_manager');

        $volume = $volumeManager->getByFileId($fileId);
        $file = $volume->findFile($fileId, $fileVersion);
        $volume->setFileMetasets($file, $ids, $this->getUser()->getId());

        return new ResultResponse(true, 'Set added.');
    }
}
