<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Phlexible\Bundle\MediaSiteBundle\Entity\Folder;
use Phlexible\Bundle\MediaSiteBundle\Folder\FolderInterface;

/**
 * Folder usage
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="media_folder_usage")
 */
class FolderUsage
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string", length=36, options={"fixed"=true})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="usage_type", type="string", length=255)
     */
    private $usageType;

    /**
     * @var string
     * @ORM\Column(name="usage_id", type="string", length=255)
     */
    private $usageId;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @var FolderInterface
     * @ORM\ManyToOne(targetEntity="Phlexible\Bundle\MediaSiteBundle\Entity\Folder")
     * @ORM\JoinColumn(name="folder_id", referencedColumnName="id")
     */
    private $folder;

    /**
     * @param FolderInterface $folder
     * @param string          $usageType
     * @param string          $usageId
     * @param int             $status
     */
    public function __construct(FolderInterface $folder, $usageType, $usageId, $status)
    {
        $this->folder = $folder;
        $this->usageType = $usageType;
        $this->usageId = $usageId;
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return FolderInterface
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * @return string
     */
    public function getUsageType()
    {
        return $this->usageType;
    }

    /**
     * @return string
     */
    public function getUsageId()
    {
        return $this->usageId;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Return array represenattion of this usage
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'folderId'  => $this->folder->getId(),
            'usageType' => $this->usageType,
            'usageId'   => $this->usageId,
            'status'    => $this->status,
        );
    }
}