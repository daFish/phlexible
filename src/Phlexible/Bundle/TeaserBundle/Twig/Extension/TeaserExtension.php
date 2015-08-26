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
use Phlexible\Bundle\TeaserBundle\Area\AreaManager;
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
     * @var AreaManager
     */
    private $areaManager;

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
     * @param AreaManager                   $areaManager
     * @param RequestStack                  $requestStack
     */
    public function __construct(
        ElementService $elementService,
        ElementSourceManagerInterface $elementSourceManager,
        AreaManager $areaManager,
        RequestStack $requestStack
    )
    {
        $this->elementService = $elementService;
        $this->elementSourceManager = $elementSourceManager;
        $this->areaManager = $areaManager;
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

            $element = $this->elementService->findElement($node->getContentId());
            $elementtypeId = $element->getElementtypeId();
            $elementtype = $this->elementSourceManager->findElementtype($elementtypeId);

            if ($this->areas === null) {
                $this->areas = array();
                // TODO: switch to type manager
                foreach ($this->elementSourceManager->findElementtypesByType('layout') as $layoutarea) {
                    #if (in_array($elementtype, $this->elementService->findAllowedParents($layoutarea))) {
                    #    $this->areas[$layoutarea->getName()] = $layoutarea;
                    #}
                }
            }

            if (!isset($this->areas[$name])) {
                return $this->teasers[$name] = array();
            }
            $area = $this->areas[$name];

            $teasers = $this->areaManager->findCascadingByAreaAndNode($area, $node, false);

            if (!count($teasers)) {
                return $this->teasers[$name] = array();
            }

            $this->teasers[$name] = array(
                'name'    => $area->getName(),
                'teasers' => $teasers
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
