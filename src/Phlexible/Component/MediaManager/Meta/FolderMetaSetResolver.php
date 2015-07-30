<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaManager\Meta;

use Phlexible\Component\MediaManager\Volume\ExtendedFolderInterface;
use Phlexible\Component\MetaSet\Domain\MetaSet;
use Phlexible\Component\MetaSet\Model\MetaSetManagerInterface;

/**
 * Folder meta set resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FolderMetaSetResolver
{
    /**
     * @var MetaSetManagerInterface
     */
    private $metaSetManager;

    /**
     * @param MetaSetManagerInterface $metaSetManager
     */
    public function __construct(MetaSetManagerInterface $metaSetManager)
    {
        $this->metaSetManager = $metaSetManager;
    }

    /**
     * @param ExtendedFolderInterface $folder
     *
     * @return \Phlexible\Component\MetaSet\Domain\MetaSet[]
     */
    public function resolve(ExtendedFolderInterface $folder)
    {
        $metaSets = array();
        foreach ($folder->getMetasets() as $metaSetId) {
            $metaSet = $this->metaSetManager->find($metaSetId);
            if ($metaSet) {
                $metaSets[] = $metaSet;
            }
        }

        return $metaSets;
    }
}
