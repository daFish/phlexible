<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\ImageExtractor;

use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * Raw image extractor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RawImageExtractor implements ImageExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(FileInterface $file)
    {
        return strtolower($file->getAttribute('assettype')) === 'image' || strtolower($file->getAttribute('documenttype')) === 'pdf';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(FileInterface $file)
    {
        return $file->getPhysicalPath();
    }
}