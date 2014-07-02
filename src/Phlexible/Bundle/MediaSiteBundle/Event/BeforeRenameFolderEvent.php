<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Event;

use Phlexible\Bundle\MediaSiteBundle\Driver\Action\RenameFolderAction;

/**
 * Before rename folder event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BeforeRenameFolderEvent extends AbstractActionEvent
{
    /**
     * @return RenameFolderAction
     */
    public function getAction()
    {
        parent::getAction();
    }
}