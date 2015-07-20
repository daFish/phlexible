<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\EventListener;

use Phlexible\Bundle\GuiBundle\Event\GetMenuEvent;
use Phlexible\Bundle\GuiBundle\Menu\MenuItem;
use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Get menu listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GetMenuListener
{
    /**
     * @var SiterootManagerInterface
     */
    private $siterootManager;

    /**
     * @var TreeManagerInterface
     */
    private $treeManager;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param SiterootManagerInterface      $siterootManager
     * @param TreeManagerInterface          $treeManager
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        SiterootManagerInterface $siterootManager,
        TreeManagerInterface $treeManager,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->siterootManager = $siterootManager;
        $this->treeManager = $treeManager;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param GetMenuEvent $event
     */
    public function onGetMenu(GetMenuEvent $event)
    {
        $items = $event->getItems();

        foreach ($this->siterootManager->findAll() as $siteroot) {
            $tree = $this->treeManager->getBySiteRootId($siteroot->getId());
            $root = $tree->getRoot();

            if (!$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') &&
                !$this->authorizationChecker->isGranted(array('permission' => 'VIEW'), $root)) {
                continue;
            }

            $menuItem = new MenuItem('tree', 'trees');
            $menuItem->setParameters(
                array(
                    'siterootId' => $siteroot->getId(),
                    'title'      => $siteroot->getTitle(),
                )
            );

            $items->set('siteroot_' . $siteroot->getId(), $menuItem);
        }
    }
}
