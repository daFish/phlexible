<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Phlexible\Bundle\MediaSiteBundle\Model\File as BaseFile;

/**
 * File
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="media_site_file")
 */
class File extends BaseFile
{
    /**
     * @var string
     * @ORM\Column(name="asset_type", type="string")
     */
    private $assetType;

    /**
     * @var string
     * @ORM\Column(name="document_type", type="string")
     */
    private $documentType;

    /**
     * @param string $assetType
     *
     * @return $this
     */
    public function setAssetType($assetType)
    {
        $this->assetType = $assetType;

        return $this;
    }

    /**
     * @return string
     */
    public function getAssetType()
    {
        return $this->assetType;
    }

    /**
     * @param string $documentType
     *
     * @return $this
     */
    public function setDocumentType($documentType)
    {
        $this->documentType = $documentType;

        return $this;
    }

    /**
     * @return string
     */
    public function getDocumentType()
    {
        return $this->documentType;
    }
}