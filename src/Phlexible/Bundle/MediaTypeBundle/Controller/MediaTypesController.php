<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTypeBundle\Controller;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\Prefix;
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
 * @Prefix("/mediatype")
 * @NamePrefix("phlexible_mediatype_")
 */
class MediaTypesController extends FOSRestController
{
    /**
     * Get media types
     *
     * @return Response
     *
     * @ApiDoc()
     */
    public function getMediatypesAction()
    {
        $mediaTypeManager = $this->get('phlexible_media_type.media_type_manager');
        $iconResolver = $this->get('phlexible_media_type.icon_resolver');

        $mediaTypes = $mediaTypeManager->findAll();
        /*
        foreach ($mediaTypeManager->findAll() as $mediaType) {
            $mediaTypes[] = [
                'id'        => $mediaType->getName(),
                'key'       => $mediaType->getName(),
                'upperkey'  => strtoupper($mediaType->getName()),
                'type'      => $mediaType->getCategory(),
                'de'        => $mediaType->getTitle('de'),
                'en'        => $mediaType->getTitle('en'),
                'mimetypes' => $mediaType->getMimetypes(),
                'icon16'    => (bool) $iconResolver->resolve($mediaType, 16),
                'icon32'    => (bool) $iconResolver->resolve($mediaType, 32),
                'icon48'    => (bool) $iconResolver->resolve($mediaType, 48),
                'icon256'   => (bool) $iconResolver->resolve($mediaType, 256),
            ];
        }
        */

        return $this->handleView($this->view(
            array(
                'mediatypes' => $mediaTypes,
                'count'      => count($mediaTypes),
            )
        ));
    }
}
