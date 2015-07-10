<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Configurator;

use Phlexible\Bundle\CmsBundle\Configurator\Configuration;
use Phlexible\Bundle\CmsBundle\Configurator\ConfiguratorInterface;
use Phlexible\Bundle\CmsBundle\Event\ConfigureEvent;
use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Model\ElementSourceManagerInterface;
use Phlexible\Bundle\TeaserBundle\Model\TeaserManagerInterface;
use Phlexible\Bundle\TeaserBundle\TeaserEvents;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Layout area configurator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LayoutareaConfigurator implements ConfiguratorInterface
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var ElementSourceManagerInterface
     */
    private $elementSourceManager;

    /**
     * @var TeaserManagerInterface
     */
    private $teaserManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ElementService                $elementService
     * @param ElementSourceManagerInterface $elementSourceManager
     * @param TeaserManagerInterface        $teaserManager
     * @param EventDispatcherInterface      $dispatcher
     * @param LoggerInterface               $logger
     */
    public function __construct(
        ElementService $elementService,
        ElementSourceManagerInterface $elementSourceManager,
        TeaserManagerInterface $teaserManager,
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger)
    {
        $this->elementService = $elementService;
        $this->elementSourceManager = $elementSourceManager;
        $this->teaserManager = $teaserManager;
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(Request $request, Configuration $renderConfiguration)
    {
        return;
        if (!$renderConfiguration->hasFeature('treeNode')) {
            return;
        }

        $elementtypeId = $renderConfiguration->get('contentElement')->getElementtypeId();
        $elementtype = $this->elementSourceManager->findElementtype($elementtypeId);

        $layouts = array();
        $layoutareas = array();
        foreach ($this->elementSourceManager->findElementtypesByType('layout') as $layoutarea) {
            if (in_array($elementtype, $this->elementService->findAllowedParents($layoutarea))) {
                $layoutareas[] = $layoutarea;
            }
        }

        $treeNode = $renderConfiguration->get('treeNode');
        $tree = $treeNode->getTree();
        $treeNodePath = $tree->getPath($treeNode);

        $language = $request->getLocale();
        $availableLanguages = $request->attributes->get('availableLanguages');
        $isPreview = true;

        $areas = array();

        foreach ($layoutareas as $layoutarea) {
            //$beforeAreaEvent = new Brainbits_Event_Notification(new stdClass(), 'before_area');
            //$this->_dispatcher->dispatch($beforeAreaEvent);

            //$templateFilename = '';
            //$templates = $layoutElementTypeVersion->getTemplates();

            //if (count($templates))
            //{
            //    $template = current($templates);
            //    $templateFilename = $template->getFilename();
            //}

            //$this->_debugTime('initTeasers - Layoutarea');
            //$this->_debugLine('Layoutarea: ' . $layoutElementTypeVersion->getTitle(), 'notice');

            $teasers = $this->teaserManager->findCascadingForLayoutAreaAndNode($layoutarea, $treeNodePath, false);

            $areas[$layoutarea->getUniqueId()] = array(
                'title'    => $layoutarea->getTitle(),
                'uniqueId' => $layoutarea->getUniqueId(),
                'children' => $teasers
            );

            //$areaEvent = new Brainbits_Event_Notification(new stdClass(), 'area');
            //$this->_dispatcher->dispatch($areaEvent);
        }

        $renderConfiguration
            ->addFeature('layoutarea')
            ->setVariable('teasers', $areas);

        $event = new ConfigureEvent($renderConfiguration);
        $this->dispatcher->dispatch(TeaserEvents::CONFIGURE_LAYOUTAREA, $event);
    }
}
