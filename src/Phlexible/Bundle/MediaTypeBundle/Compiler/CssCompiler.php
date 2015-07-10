<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTypeBundle\Compiler;

use Temp\MediaClassifier\Model\MediaTypeCollection;

/**
 * CSS generator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CssCompiler implements CompilerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getClassname()
    {
        return 'mediatype';
    }

    /**
     * {@inheritdoc}
     */
    public function compile(MediaTypeCollection $mediaTypes)
    {
        $sizes = array(16 => 'small'); //, 32 => 'medium', 48 => 'tile');

        $classname = $this->getClassname();

        $styles = array();
        foreach ($mediaTypes->all() as $mediaType) {
            $name = $mediaType->getName();

            foreach ($sizes as $size => $sizeTitle) {
                $styles[] = sprintf(
                    '.p-%s-%s-small {background-image:url(//BUNDLES_PATH/phlexiblemediatype/mimetypes16/%s.gif) !important;}',
                    $classname,
                    str_replace(':', '-', (string) $mediaType),
                    $name
                );
            }
        }

        return implode(PHP_EOL, $styles);
    }
}
