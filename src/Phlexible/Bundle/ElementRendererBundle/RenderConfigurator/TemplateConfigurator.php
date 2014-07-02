<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle\RenderConfigurator;

use Dwoo\Template\File;
use Phlexible\Bundle\ElementRendererBundle\RenderConfiguration;
use Phlexible\Bundle\TemplateBundle\TemplateRepository as TemplateRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Template configurator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TemplateConfigurator implements ConfiguratorInterface
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
     * @var TemplateRepository
     */
    private $templateRepository;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param LoggerInterface          $logger
     * @param TemplateRepository       $templateRepository
     */
    public function __construct(EventDispatcherInterface $dispatcher, LoggerInterface $logger, TemplateRepository $templateRepository)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
        $this->templateRepository = $templateRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(Request $request, RenderConfiguration $renderConfiguration)
    {
        if (!$renderConfiguration->hasFeature('templateFile')) {
            return;
        }

        $templateFile = $renderConfiguration->get('templateFile');
        $template = new File('/Users/swentz/Sites/phlexible/brainbits/templates/html/' . $templateFile . '.html.dwoo');

        $renderConfiguration
            ->addFeature('template')
            ->set('template', $template);
    }

}