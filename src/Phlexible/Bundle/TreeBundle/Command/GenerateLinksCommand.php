<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Command;

use Phlexible\Component\Tree\LiveTreeContext;
use Phlexible\Component\Tree\WorkingTreeContext;
use Phlexible\Component\Tree\TreeIterator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generate links command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GenerateLinksCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('tree:generate-links')
            ->setDescription('Generate links.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $treeManager = $this->getContainer()->get('phlexible_tree.tree_manager');
        $linkExtractor = $this->getContainer()->get('phlexible_tree.link_extractor');
        $versionStrategy = $this->getContainer()->get('phlexible_tree.mediator.preview_version_strategy');
        $elementMediator = $this->getContainer()->get('phlexible_tree.mediator.element');
        $elementMediator->setVersionStrategy($versionStrategy);

        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $nodeLinkRepository = $em->getRepository('PhlexibleTreeBundle:NodeLink');

        $locales = array('de');

        foreach ($locales as $locale) {
            $treeContext = new LiveTreeContext($locale);

            foreach ($treeManager->getAll($treeContext) as $tree) {
                $rii = new \RecursiveIteratorIterator(new TreeIterator($tree), \RecursiveIteratorIterator::SELF_FIRST);
                foreach ($rii as $node) {
                    $versions = $node->getContentVersions();
                    if (!$versions) {
                        $output->writeln("Skipping {$node->getId()}, no versions");
                        continue;
                    }

                    foreach ($versions as $version) {
                        foreach ($nodeLinkRepository->findBy(array('nodeId' => $node->getId(), 'language' => $locale, 'version' => $version)) as $link) {
                            $em->remove($link);
                        }

                        foreach ($linkExtractor->extract($node, $locale, $version) as $extractedLink) {
                            $em->persist($extractedLink);
                        }
                    }

                    $em->flush();
                }
            }
        }

        return 0;
    }
}

