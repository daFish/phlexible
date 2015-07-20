<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Mediator;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementBundle\Proxy\ClassManager;
use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Mediator\VersionStrategy\VersionStrategyInterface;
use Phlexible\Bundle\TeaserBundle\Model\TeaserManagerInterface;

/**
 * Element mediator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementTeaserMediator implements TeaserMediatorInterface
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var VersionStrategyInterface
     */
    private $versionStrategy;

    /**
     * @var ClassManager
     */
    private $classManager;

    /**
     * @param ElementService           $elementService
     * @param VersionStrategyInterface $versionStrategy
     * @param ClassManager             $classManager
     */
    public function __construct(ElementService $elementService, VersionStrategyInterface $versionStrategy, ClassManager $classManager)
    {
        $this->elementService = $elementService;
        $this->versionStrategy = $versionStrategy;
        $this->classManager = $classManager;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(Teaser $teaser)
    {
        return $teaser->getType() === 'element';
    }

    /**
     * {@inheritdoc}
     */
    public function getField(TeaserManagerInterface $teaserManager, Teaser $teaser, $field, $language)
    {
        $elementVersion = $this->versionStrategy->find($teaserManager, $teaser, $language);

        return $elementVersion->getMappedField($field, $language);
    }

    /**
     * {@inheritdoc}
     *
     * @return ElementVersion
     */
    public function getContentDocument(TeaserManagerInterface $teaserManager, Teaser $teaser, $language)
    {
        $elementVersion = $this->versionStrategy->find($teaserManager, $teaser, $language);

        return $this->classManager->create($elementVersion);
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate(TeaserManagerInterface $teaserManager, Teaser $teaser)
    {
        $element = $this->elementService->findElement($teaser->getTypeId());
        $elementSource = $this->elementService->findElementSource($element->getElementtypeId());

        $template = $elementSource->getTemplate();

        if ($template) {
            return $template;
        }

        return '::' . $elementSource->getName() . '.html.twig';
    }
}
