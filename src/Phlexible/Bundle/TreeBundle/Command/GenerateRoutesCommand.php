<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Command;

use Phlexible\Bundle\TreeBundle\Entity\PageNode;
use Phlexible\Bundle\TreeBundle\RouteGenerator\LanguagePathDecorator;
use Phlexible\Bundle\TreeBundle\RouteGenerator\NodeIdPathDecorator;
use Phlexible\Bundle\TreeBundle\RouteGenerator\PathGenerator;
use Phlexible\Bundle\TreeBundle\RouteGenerator\RouteGenerator;
use Phlexible\Bundle\TreeBundle\RouteGenerator\SuffixPathDecorator;
use Phlexible\Component\Site\Domain\Site;
use Phlexible\Component\Tree\TreeIterator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generate routes command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GenerateRoutesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('tree:generate-routes')
            ->setDescription('Generate routes.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $siteManager = $this->getContainer()->get('phlexible_siteroot.siteroot_manager');
        $treeManager = $this->getContainer()->get('phlexible_tree.tree_manager');
        $routeManager = $this->getContainer()->get('phlexible_tree.doctrine.route_manager');

        $language = 'de';
        $routeGenerator = new RouteGenerator(
            new PathGenerator(
                array(
                    new LanguagePathDecorator(),
                    new NodeIdPathDecorator(),
                    new SuffixPathDecorator(),
                )
            )
        );

        $routes = array();
        foreach ($routeManager->findAll() as $route) {
            $routes[$route->getName()] = $route;
        }

        foreach ($siteManager->findAll() as $siteroot) {
            /* @var $siteroot Site */

            $treeId = $siteroot->getId();
            $tree = $treeManager->getBySiteRootId($treeId);
            $tree->setDefaultLanguage($language);

            $rii = new \RecursiveIteratorIterator(new TreeIterator($tree), \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($rii as $node) {
                if ($tree->isPublished($node, $language)) {
                    if (!$node->getNode() instanceof PageNode) {
                        continue;
                    }
                    $newRoute = $routeGenerator->generateNodeRoute($node, $siteroot, $language);
                    if (isset($routes[$newRoute->getName()])) {
                        $route = $routes[$newRoute->getName()];
                        $route->setHost($newRoute->getHost());
                        $route->setOptions($newRoute->getOptions());
                        $route->setCondition($newRoute->getCondition());
                        $route->setDefaults($newRoute->getDefaults());
                        $route->setMethods($newRoute->getDefaults());
                        $route->setName($newRoute->getName());
                        $route->setPath($newRoute->getPath());
                        $route->setRequirements($newRoute->getRequirements());
                        $route->setSchemes($newRoute->getSchemes());
                        unset($routes[$route->getName()]);
                        $output->writeln('UPDATE: ' . $route->getName() . ' => ' . current($route->getSchemes()) . '://' . $route->getHost() . $route->getPath());
                    } else {
                        $route = $newRoute;
                        $output->writeln('NEW: ' . $route->getName() . ' => ' . current($route->getSchemes()) . '://' . $route->getHost() . $route->getPath());
                    }

                    $routeManager->updateRoute($route);
                }
            }

            foreach ($siteroot->getEntryPoints() as $name => $entryPoint) {
                $node = $treeManager->getByNodeId($entryPoint['nodeId'])->get($entryPoint['nodeId']);
                $route = $routeGenerator->generateEntryPointRoute($node, $siteroot->getId(), $entryPoint['hostname'], $name, $language);
                if (isset($all[$route->getName()])) {
                    $routeManager->deleteRoute($routes[$route->getName()]);
                    unset($routes[$route->getName()]);
                }
                $output->writeln($route->getName() . ' => ' . current($route->getSchemes()) . '://' . $route->getHost() . $route->getPath());
                $routeManager->updateRoute($route);
            }
        }

        foreach ($routes as $route) {
            $output->writeln('REMOVE: ' . $route->getName() . ' => ' . current($route->getSchemes()) . '://' . $route->getHost() . $route->getPath());
            $routeManager->deleteRoute($route);
        }

        return 0;
    }
}

