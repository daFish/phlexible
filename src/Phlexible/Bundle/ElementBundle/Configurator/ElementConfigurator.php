<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Configurator;

use Phlexible\Bundle\CmsBundle\Configurator\Configuration;
use Phlexible\Bundle\CmsBundle\Configurator\ConfiguratorInterface;
use Phlexible\Bundle\CmsBundle\Event\ConfigureEvent;
use Phlexible\Bundle\ElementBundle\ContentElement\ContentElement;
use Phlexible\Bundle\ElementBundle\ContentElement\ContentElementLoader;
use Phlexible\Bundle\ElementBundle\ElementEvents;
use Phlexible\Bundle\ElementBundle\ElementService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Element configurator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementConfigurator implements ConfiguratorInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param EventDispatcherInterface      $dispatcher
     * @param LoggerInterface               $logger
     * @param ElementService                $elementService
     * @param ContentElementLoader          $loader
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger,
        ElementService $elementService,
        ContentElementLoader $loader,
        AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
        $this->elementService = $elementService;
        $this->loader = $loader;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(Request $request, Configuration $renderConfiguration)
    {
        if (!$renderConfiguration->hasFeature('eid')) {
            return;
        }

        $elementEid = $renderConfiguration->get('eid');
        $elementVersion = $renderConfiguration->get('version');
        $elementLanguage = $renderConfiguration->get('language');

        /*
        $versionStrategy = new OnlineVersionStrategy($this->elementService);
        $availableLanguages = $request->attributes->get('availableLanguages', array('de'));
        $elementLanguage = $versionStrategy->findLanguage($request, $element, $availableLanguages);
        */

        $contentElement = $this->loader->load(
            $elementEid,
            $elementVersion,
            $elementLanguage
        );

        if (!$contentElement) {
            return false;
        }

        $renderConfiguration
            ->addFeature('element')
            ->setVariable('contentElement', $contentElement)
            ->setVariable('content', $contentElement->getStructure());

        if (!$renderConfiguration->hasFeature('template')) {
            if ($contentElement->getElementtypeTemplate()) {
                $template = $contentElement->getElementtypeTemplate();
            } else {
                $template = '::' . $contentElement->getElementtypeUniqueId() . '.html.twig';
            }

            $renderConfiguration
                ->addFeature('template')
                ->setVariable('template', $template);
        }

        $event = new ConfigureEvent($renderConfiguration);
        $this->dispatcher->dispatch(ElementEvents::CONFIGURE_ELEMENT, $event);

        return;

        $eid = $renderConfiguration->get('eid');
        $element = $this->elementService->findElement($eid);

        $versionStrategy = new OnlineVersionStrategy($this->elementService);

        $availableLanguages = $request->attributes->get('availableLanguages', array('de'));
        $elementLanguage = $versionStrategy->findLanguage($request, $element, $availableLanguages);

        if (!$elementLanguage) {
            $msg = "Element {$element->getEid()} not available in languages " . implode(',', $availableLanguages);
            $this->logger->error($msg);

            throw new Makeweb_Renderers_Html_Exception_ElementNotOnlineException($msg);
        }

        $elementVersion = $versionStrategy->findVersion($request, $element, $elementLanguage);

        if (!$elementVersion) {
            $msg = "Element {$element->getEid()} not available in language $elementLanguage";
            $this->logger->error($msg);

            throw new Makeweb_Renderers_Html_Exception_ElementNotOnlineException($msg);
        }

        $elementtype = $this->elementService->findElementtype($element);

        /*
        if ($forward = $elementVersion->getMappedField('forward'))
        {
            $forwardTid = $forward;
            $forwardTreeNode = $tree->get($forwardTid);
            $forwardUrl = $this->router->generate($forwardTreeNode);

            if (!$forwardUrl) {
                throw new Exception("Missing forward target, cannot create link");
            }

            $this->_response->setHttpResponseCode(301)
                ->setHeader('Location', $forwardUrl);

            return false;
        }
        */

        /*
        if (in_array($elementtype->getType(), array(ElementtypeVersion::TYPE_STRUCTURE, ElementtypeVersion::TYPE_PART))) {
            throw new \Makeweb_Renderers_Exception('Not a viewable node.', 404);
        }
        */

        if ($versionStrategy->getName() === 'latest') {
            if (!$this->authorizationChecker->isGranted('VIEW', $node)) {
                $this->logger->debug('403 Forbidden du to missing VIEW content right');

                throw new \Makeweb_Renderers_Exception('Forbidden', 403);
            }
        }

        $elementStructure = $this->elementService->findElementStructure($elementVersion, $elementLanguage);

        $this->logger->debug('Element: ' . $elementVersion->getPageTitle($elementLanguage));

        $contentElement = new ContentElement(
            $element->getEid(),
            $element->getUniqueId(),
            $elementtype->getId(),
            $elementtype->getUniqueId(),
            $elementtype->getType(),
            $elementVersion->getVersion(),
            $elementLanguage,
            $elementVersion->getMappedFields()[$elementLanguage],
            $elementStructure
        );

        $template = $contentElement->getElementtypeUniqueId();

        $renderConfiguration
            ->addFeature('element')
            ->setVariable('contentElement', $contentElement)
            ->setVariable('content', $data->contentElement->getStructure())
            ->setVariable('contentLanguages', array('de'));

        if (!$renderConfiguration->hasFeature('template')) {
            $renderConfiguration
                ->addFeature('template')
                ->setVariable('template', $template);
        }

        $event = new ConfigureEvent($renderConfiguration);
        $this->dispatcher->dispatch(ElementEvents::CONFIGURE_ELEMENT, $event);
    }
}
