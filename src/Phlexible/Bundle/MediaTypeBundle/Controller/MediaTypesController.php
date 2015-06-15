<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaTypeBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * Media types controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_MEDIA_TYPES')")
 * @Rest\NamePrefix("phlexible_api_mediatype_")
 */
class MediaTypesController extends FOSRestController
{
    /**
     * Get media types
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a collection of MediaType",
     *   section="mediatype",
     *   resource=true,
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */
    public function getMediatypesAction()
    {
        $mediaTypeManager = $this->get('phlexible_media_type.media_type_manager');
        $mediaTypes = $mediaTypeManager->findAll();

        return array(
            'mediatypes' => array_values($mediaTypes),
            'count'      => count($mediaTypes),
        );
    }
}
