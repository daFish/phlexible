<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\EventListener;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Bundle\SiterootBundle\Event\SiterootEvent;
use Phlexible\Bundle\TreeBundle\Model\NodeManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeManagerInterface;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;
use Phlexible\Component\Elementtype\ElementtypeService;
use Phlexible\Component\Elementtype\Model\ElementtypeStructure;
use Phlexible\Component\Elementtype\Model\ElementtypeStructureNode;

/**
 * Siteroot listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiterootListener
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var ElementtypeService
     */
    private $elementtypeService;

    /**
     * @var TreeManagerInterface
     */
    private $treeManager;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var string
     */
    private $masterLanguage;

    /**
     * @param ElementService       $elementService
     * @param ElementtypeService   $elementtypeService
     * @param TreeManagerInterface $treeManager
     * @param UserManagerInterface $userManager
     * @param string               $masterLanguage
     */
    public function __construct(
        ElementService $elementService,
        ElementtypeService $elementtypeService,
        TreeManagerInterface $treeManager,
        UserManagerInterface $userManager,
        $masterLanguage)
    {
        $this->elementService = $elementService;
        $this->elementtypeService = $elementtypeService;
        $this->treeManager = $treeManager;
        $this->userManager = $userManager;
        $this->masterLanguage = $masterLanguage;
    }

    /**
     * @param SiterootEvent $event
     */
    public function onCreateSiteroot(SiterootEvent $event)
    {
        $siteroot = $event->getSiteroot();

        $elementtypeStructure = new ElementtypeStructure();

        $root = new ElementtypeStructureNode();
        $root
            ->setDsId(Uuid::generate())
            ->setName('root')
            ->setType('root');

        $tab = new ElementtypeStructureNode();
        $tab
            ->setParentNode($root)
            ->setParentDsId($root->getDsId())
            ->setDsId(Uuid::generate())
            ->setName('data')
            ->setType('tab')
            ->setLabels(array('fieldLabel' => array('de' => 'Daten', 'en' => 'Data')))
            ->setConfiguration(array())
            ->setValidation(array());

        $textfield = new ElementtypeStructureNode();
        $textfield
            ->setParentNode($tab)
            ->setParentDsId($tab->getDsId())
            ->setDsId(Uuid::generate())
            ->setName('title')
            ->setType('textfield')
            ->setLabels(array('fieldLabel' => array('de' => 'Titel', 'en' => 'Title')))
            ->setConfiguration(array('required' => 'always'))
            ->setValidation(array());

        $elementtypeStructure
            ->addNode($root)
            ->addNode($tab)
            ->addNode($textfield);

        $mappings = array(
            'backend' => array(
                'fields' => array(
                    array('ds_id' => $textfield->getDsId(), 'field' => 'Title', 'index' => 1)
                ),
                'pattern' => '$1'
            )
        );

        $user = $this->userManager->find($siteroot->getModifyUserId());

        $elementtype = $this->elementtypeService->createElementtype(
            'structure',
            'site_root_' . $siteroot->getId(),
            'Site root ' . $siteroot->getTitle(),
            'www_root.gif',
            $elementtypeStructure,
            $mappings,
            $user->getUsername(),
            false
        );

        $elementSource = $this->elementService->createElementSource($elementtype);

        $elementVersion = $this->elementService->createElement($elementSource, $this->masterLanguage, $siteroot->getModifyUserId());

        $this->treeManager->createTree(
            $siteroot->getId(),
            'element-structure',
            $elementVersion->getElement()->getEid(),
            $elementVersion->getElement()->getCreateUserId()
        );
    }
}
