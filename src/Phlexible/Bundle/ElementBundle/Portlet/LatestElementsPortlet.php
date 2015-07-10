<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Portlet;

use Doctrine\DBAL\Connection;
use Phlexible\Bundle\DashboardBundle\Portlet\Portlet;
use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Icon\IconResolver;
use Phlexible\Bundle\TreeBundle\Model\NodeManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Latest elements portlet
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LatestElementsPortlet extends Portlet
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var NodeManagerInterface
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
     * @param ElementService       $elementService
     * @param NodeManagerInterface $nodeManager
     * @param IconResolver         $iconResolver
     * @param Connection           $connection
     * @param int                  $numItems
     */
    public function __construct(
        TranslatorInterface $translator,
        ElementService $elementService,
        NodeManagerInterface $nodeManager,
        IconResolver $iconResolver,
        Connection $connection,
        $numItems)
    {
        $this
            ->setId('elements-portlet')
            ->setTitle($translator->trans('elements.latest_element_changes', array(), 'gui'))
            ->setClass('Phlexible.elements.portlet.LatestElements')
            ->setIconClass('p-element-component-icon')
            ->setRole('ROLE_ELEMENTS');

        $this->elementService = $elementService;
        $this->nodeManager = $nodeManager;
        $this->iconResolver = $iconResolver;
        $this->connection = $connection;
        $this->numItems = $numItems;
    }

    /**
     * Return Portlet data
     *
     * @return array
     */
    public function getData()
    {
        $nodes = $this->nodeManager->findBy(array(), array('updatedAt' => 'DESC', $this->numItems));

        foreach ($nodes as $node) {
            $element = $this->elementService->findElement($node->getTypeId());
            $elementVersion = $this->elementService->findLatestElementVersion($element);

            $baseTitle = $elementVersion->getBackendTitle('de', 'en');
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

            $data[] = array(
                'ident'    => $node->getId(),
                'eid'      => $node->getTypeId(),
                'language' => 'de',
                'version'  => 1,
                'title'    => strip_tags($title),
                'icon'     => $this->iconResolver->resolveNode($node, 'de'),
                'time'     => strtotime($elementVersion->getCreatedAt()->format('Y-m-d H:i:s')),
                'author'   => $elementVersion->getCreateUserId(),
                'menu'     => $menu
            );
        }

        return $data;
    }
}
