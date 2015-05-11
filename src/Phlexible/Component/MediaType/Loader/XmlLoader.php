<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaType\Loader;

use Brainbits\Mime\InternetMediaType;
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
    public function supports($file)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function load($filename)
    {
        $xml = simplexml_load_file($filename);

        $mediaType = new MediaType();

        $attrs = $xml->attributes();
        $mediaType
            ->setName((string) $attrs['key'])
            ->setCategory((string) $xml->type)
            ->setSvg((string) $xml->svg);

        if ($xml->titles->count()) {
            if ($xml->titles->title->count()) {
                foreach ($xml->titles->title as $titleNode) {
                    $titleNodeAttrs = $titleNode->attributes();
                    $lang = (string) $titleNodeAttrs['lang'];
                    $title = (string) $titleNode;
                    if ($lang && $title) {
                        $mediaType->setTitle($lang, $title);
                    }
                }
            }
        }

        if ($xml->mimetypes->count()) {
            if ($xml->mimetypes->mimetype->count()) {
                foreach ($xml->mimetypes->mimetype as $mimetypeNode) {
                    $mimetype = (string) $mimetypeNode;
                    if ($mimetype) {
                        $internetMediaType = InternetMediaType::fromString($mimetype);
                        $mediaType->addMimetype($internetMediaType);
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
                        $mediaType->setIcon($size, $icon);
                    }
                }
            }
        }

        return $mediaType;
    }

}
