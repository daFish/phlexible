<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Portlet;

use Phlexible\Bundle\DashboardBundle\Domain\Portlet;
use Phlexible\Bundle\TreeBundle\Icon\IconResolver;
use Phlexible\Bundle\TreeBundle\Model\TreeManagerInterface;
use Phlexible\Component\Node\Model\NodeManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Latest node changes portlet
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LatestNodeChangesPortlet extends Portlet
{
    /**
     * @var TreeManagerInterface
     */
    private $treeManager;

    /**
     * @var \Phlexible\Component\Node\Model\NodeManagerInterface
     */
    private $nodeManager;

    /**
     * @var IconResolver
     */
    private $iconResolver;

    /**
     * @var int
     */
    private $numItems;

    /**
     * @param TranslatorInterface  $translator
     * @param TreeManagerInterface $treeManager
     * @param NodeManagerInterface $nodeManager
     * @param IconResolver         $iconResolver
     * @param int                  $numItems
     */
    public function __construct(
        TranslatorInterface $translator,
        TreeManagerInterface $treeManager,
        NodeManagerInterface $nodeManager,
        IconResolver $iconResolver,
        $numItems)
    {
        $this
            ->setId('tree-latest-node-changes-portlet')
            ->setTitle($translator->trans('elements.latest_element_changes', array(), 'gui'))
            ->setClass('Phlexible.tree.portlet.LatestNodeChanges')
            ->setIconClass('p-tree-component-icon')
            ->setRole('ROLE_ELEMENTS');

        $this->treeManager = $treeManager;
        $this->nodeManager = $nodeManager;
        $this->iconResolver = $iconResolver;
        $this->numItems = $numItems;
    }

    /**
     * Return Portlet data
     *
     * @return array
     */
    public function getData()
    {
        // TODO: modifiedAt
        $rawNodes = $this->nodeManager->findBy(array(), array('createdAt' => 'DESC'), $this->numItems);

        $language = 'de';

        $data = array();
        foreach ($rawNodes as $rawNode) {
            $tree = $this->treeManager->getByNodeId($rawNode->getId());
            $node = $tree->get($rawNode->getId());

            $baseTitle = $node->getField('backend', $language);
            $baseTitleArr = str_split($baseTitle, 16);
            $title = '';

            $first = true;
            foreach ($baseTitleArr as $chunk) {
                $title .= ($first ? '<wbr />' : '') . $chunk;
                $first = false;
            }

            $title .= ' [' . $node->getId() . ']';
            /*
                $i = 0;
                do
                {
                    $title .= ($i ? '<wbr />' : '') . substr($baseTitle, $i, $i + 16);
                    $i += 16;
                }
                while($i <= strlen($baseTitle));
            */

            /*
            $menuItem = new MWF_Core_Menu_Item_Panel();
            $menuItem->setIdentifier('Makeweb_elements_MainPanel_' . $siteroot->getTitle())
                ->setText($siteroot->getTitle())
                ->setIconClass('p-element-component-icon')
                ->setPanel('Makeweb.elements.MainPanel')
                ->setParam('siteroot_id', $siteroot->getId())
                ->setParam('title', $siteroot->getTitle())
                ->setParam('id', $node->getId())
                ->setParam('start_tid_path', '/' . implode('/', $node->getPath()))
                ->setCheck(['elements']);

            $menu = $menuItem->get();
            */
            $menu = array();

            // TODO: modifiedAt
            $data[] = array(
                'nodeId'       => $node->getId(),
                'language'     => $language,
                'version'      => 1,
                'title'        => strip_tags($title),
                'icon'         => $this->iconResolver->resolveNode($node, $language),
                'modifiedAt'   => $node->getCreatedAt()->format('Y-m-d H:i:s'),
                'modifyUserId' => $node->getCreateUserId(),
                'menu'         => $menu
            );
        }

        return $data;
    }
}
