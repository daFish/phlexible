<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CmsBundle;

use Phlexible\Bundle\CmsBundle\DependencyInjection\Compiler\AddConfiguratorsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * CMS bundle
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleCmsBundle extends Bundle
{
    const RESOURCE_REPORTS = 'reports';
    const RESOURCE_STATISTICS = 'statistics';

    /**
     * Constructor.
     */
    public function __construct()
    {
        if (extension_loaded('suhosin')) {
            throw new \LogicException("Please deactivate the suhosin extension.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddConfiguratorsPass());
    }
}
