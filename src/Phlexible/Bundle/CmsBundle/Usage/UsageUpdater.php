<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CmsBundle\Usage;

use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;

/**
 * Usage updater
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UsageUpdater
{
    /**
     * @var FileUsageUpdater
     */
    private $fileUsageUpdater;

    /**
     * @var FolderUsageUpdater
     */
    private $folderUsageUpdater;

    /**
     * @param FileUsageUpdater   $fileUsageUpdater
     * @param FolderUsageUpdater $folderUsageUpdater
     */
    public function __construct(FileUsageUpdater $fileUsageUpdater, FolderUsageUpdater $folderUsageUpdater)
    {
        $this->fileUsageUpdater = $fileUsageUpdater;
        $this->folderUsageUpdater = $folderUsageUpdater;
    }

    /**
     * @param NodeContext $node
     *
     * @return array
     */
    public function updateUsage(NodeContext $node)
    {
        $this->fileUsageUpdater->updateUsage($node);
        $this->folderUsageUpdater->updateUsage($node);
    }
}
