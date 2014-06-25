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

/**
 * Folder meta
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="media_folder_meta")
 */
class FolderMeta
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
     * @ORM\Column(name="set_id", type="string", length=36, options={"fixed"=true})
     */
    private $setId;

    /**
     * @var string
     * @ORM\Column(name="meta_key", type="string", length=255)
     */
    private $metaKey;

    /**
     * @var string
     * @ORM\Column(name="meta_language", type="string", length=2, options={"fixed"=true})
     */
    private $metaLanguage;

    /**
     * @var string
     * @ORM\Column(name="meta_value", type="text")
     */
    private $metaValue;

    /**
     * @var Folder
     * @ORM\ManyToOne(targetEntity="Phlexible\Bundle\MediaSiteBundle\Entity\Folder")
     * @ORM\JoinColumn(name="folder_id", referencedColumnName="id")
     */
    private $folder;
}