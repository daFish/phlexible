<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\AttributeReader;

use Phlexible\Bundle\MediaSiteBundle\FileSource\PathSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\Model\AttributeBag;

/**
 * Zip extension attribute reader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ZipExtensionAttributeReader implements AttributeReaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
       return extension_loaded('zip');
    }

    /**
     * {@inheritdoc}
     */
    public function supports(PathSourceInterface $fileSource, $documenttype, $assettype)
    {
        return $documenttype === 'zip';
    }

    /**
     * {@inheritdoc}
     */
    public function read(PathSourceInterface $fileSource, $documenttype, $assettype, AttributeBag $attributes)
    {
        $filename = $fileSource->getPath();

        try {
            $zip = new \ZipArchive();
            $result = $zip->open($filename);

            if ($result === true) {
                if ($zip->comment) {
                    $attributes
                        ->set('zip.comment', $zip->comment);
                }

                if ($zip->numFiles) {
                    $attributes
                        ->set('zip.numFiles', $zip->numFiles);
                }

                if ($zip->status) {
                    $attributes
                        ->set('zip.status', $zip->status);
                }

                if ($zip->statusSys) {
                    $attributes
                        ->set('zip.statusSys', $zip->statusSys);
                }

                $zip->close();
            }
        } catch (\Exception $e) {
        }
    }
}