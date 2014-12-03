<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaType\File\Loader;

use Phlexible\Component\MediaType\Model\MediaType;

/**
 * XML media type loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlLoader implements LoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getExtension()
    {
        return 'xml';
    }

    /**
     * {@inheritdoc}
     */
    public function load($filename)
    {
        $xml = simplexml_load_file($filename);

        $documentType = new MediaType();

        $attrs = $xml->attributes();
        $documentType
            ->setName((string) $attrs['key'])
            ->setCategory((string) $xml->type);

        if ($xml->titles->count()) {
            if ($xml->titles->title->count()) {
                foreach ($xml->titles->title as $titleNode) {
                    $titleNodeAttrs = $titleNode->attributes();
                    $lang = (string) $titleNodeAttrs['lang'];
                    $title = (string) $titleNode;
                    if ($lang && $title) {
                        $documentType->setTitle($lang, $title);
                    }
                }
            }
        }

        if ($xml->mimetypes->count()) {
            if ($xml->mimetypes->mimetype->count()) {
                foreach ($xml->mimetypes->mimetype as $mimetypeNode) {
                    $mimetype = (string) $mimetypeNode;
                    if ($mimetype) {
                        $documentType->addMimetype($mimetype);
                    }
                }
            }
        }

        if ($xml->icons->count()) {
            if ($xml->icons->icon->count()) {
                foreach ($xml->icons->icon as $iconNode) {
                    $iconAttributes = $iconNode->attributes();
                    $size = (int) $iconAttributes['size'];
                    $icon = (string) $iconNode;
                    if ($icon) {
                        $documentType->setIcon($size, $icon);
                    }
                }
            }
        }

        return $documentType;
    }

}
