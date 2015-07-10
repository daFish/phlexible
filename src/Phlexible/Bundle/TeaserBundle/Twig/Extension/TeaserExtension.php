<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Twig\Extension;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Model\ElementSourceManagerInterface;
use Phlexible\Bundle\TeaserBundle\Model\TeaserManagerInterface;
use Phlexible\Bundle\TeaserBundle\Teaser\TeaserContext;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Twig teaser extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TeaserExtension extends \Twig_Extension
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
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var array
     */
    private $teasers = array();

    /**
     * @var array
     */
    private $areas = null;

    /**
     * @param ElementService                $elementService
     * @param ElementSourceManagerInterface $elementSourceManager
     * @param TeaserManagerInterface        $teaserManager
     * @param RequestStack                  $requestStack
     */
    public function __construct(
        ElementService $elementService,
        ElementSourceManagerInterface $elementSourceManager,
        TeaserManagerInterface $teaserManager,
        RequestStack $requestStack
    )
    {
        $this->elementService = $elementService;
        $this->elementSourceManager = $elementSourceManager;
        $this->teaserManager = $teaserManager;
        $this->requestStack = $requestStack;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('area', array($this, 'area')),
        );
    }

    /**
     * @param string $name
     *
     * @return \Phlexible\Bundle\TreeBundle\Node\NodeContext|null
     */
    public function area($name)
    {
        if (!isset($this->teasers[$name])) {
            $request = $this->requestStack->getCurrentRequest();

            if (!$request->attributes->has('node')) {
                throw new \InvalidArgumentException("Need a node request attribute for navigations");
            }

            $node = $request->attributes->get('node');

            $element = $this->elementService->findElement($node->getTypeId());
            $elementtypeId = $element->getElementtypeId();
            $elementtype = $this->elementSourceManager->findElementtype($elementtypeId);

            if ($this->areas === null) {
                $this->areas = array();
                foreach ($this->elementSourceManager->findElementtypesByType('layout') as $layoutarea) {
                    if (in_array($elementtype, $this->elementService->findAllowedParents($layoutarea))) {
                        $this->areas[$layoutarea->getUniqueId()] = $layoutarea;
                    }
                }
            }

            if (!isset($this->areas[$name])) {
                return $this->teasers[$name] = array();
            }
            $area = $this->areas[$name];

            $teasers = array();
            foreach ($this->teaserManager->findCascadingForLayoutAreaAndNode($area, $node, false) as $teaser) {
                $teasers[] = new TeaserContext($this->teaserManager, $teaser, $node);
            }

            if (!count($teasers)) {
                return $this->teasers[$name] = array();
            }

            $this->teasers[$name] = array(
                'title'    => $area->getTitle(),
                'uniqueId' => $area->getUniqueId(),
                'teasers'  => $teasers
            );
        }

        return $this->teasers[$name];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'phlexible_teaser';
    }
}
