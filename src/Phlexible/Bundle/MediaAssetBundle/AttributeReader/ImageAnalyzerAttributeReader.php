<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\AttributeReader;

use Brainbits\ImageAnalyzer\ImageAnalyzer;
use Phlexible\Bundle\MediaSiteBundle\FileSource\PathSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\Model\AttributeBag;

/**
 * Imagemagick based attribute reader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ImageAnalyzerAttributeReader implements AttributeReaderInterface
{
    /**
     * @var ImageAnalyzer
     */
    private $analyzer;

    /**
     * @param ImageAnalyzer $analyzer
     */
    public function __construct(ImageAnalyzer $analyzer)
    {
        $this->analyzer = $analyzer;
    }

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
    public function supports(PathSourceInterface $fileSource, $documenttype, $assettype)
    {
        return $assettype === 'image';
    }

    /**
     * {@inheritdoc}
     */
    public function read(PathSourceInterface $fileSource, $documenttype, $assettype, AttributeBag $attributes)
    {
        $filename = $fileSource->getPath();

        try {
            $imageInfo = $this->analyzer->analyze($filename);

            $attributes
                ->set('image.width', $imageInfo->getWidth())
                ->set('image.height', $imageInfo->getHeight())
                ->set('image.format', $imageInfo->getFormat())
                ->set('image.type', $imageInfo->getType())
                ->set('image.colorspace', $imageInfo->getColorspace())
                ->set('image.depth', $imageInfo->getDepth());

            if ($imageInfo->getColors()) {
                $attributes->set('image.colors', $imageInfo->getColors());
            }
            if ($imageInfo->getQuality()) {
                $attributes->set('image.quality', $imageInfo->getQuality());
            }
            if ($imageInfo->getCompression()) {
                $attributes->set('image.compression', $imageInfo->getCompression());
            }
            if ($imageInfo->getResolution()) {
                $attributes->set('image.resolution', $imageInfo->getResolution());
            }
            if ($imageInfo->getUnits()) {
                $attributes->set('image.units', $imageInfo->getUnits());
            }
            if ($imageInfo->getProfiles()) {
                $attributes->set('image.profiles', implode(',', $imageInfo->getProfiles()));
            }
        } catch (\Exception $e) {
        }
    }
}
