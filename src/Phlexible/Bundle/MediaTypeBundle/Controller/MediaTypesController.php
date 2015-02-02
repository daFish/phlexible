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

        $mediaTypes = array();
        foreach ($mediaTypeManager->findAll() as $mediaType) {
            $icons = array();
            foreach ($mediaType->getIcons() as $size => $file) {
                $filename = basename($file);
                $icons[$size] = "/bundles/phlexiblemediatype/mimetypes$size/$filename";
            }
            $mediaTypes[] = [
                'name'      => $mediaType->getName(),
                'category'  => $mediaType->getCategory(),
                'titles'    => $mediaType->getTitles(),
                'mimetypes' => $mediaType->getMimetypes(),
                'icons'     => $icons,
            ];
        }

        return $this->handleView($this->view(
            array(
                'mediatypes' => $mediaTypes,
                'count'      => count($mediaTypes),
            )
        ));
    }
}
