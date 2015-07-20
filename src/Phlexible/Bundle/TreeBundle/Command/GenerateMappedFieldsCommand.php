<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Command;

use Phlexible\Bundle\TreeBundle\Entity\NodeMappedField;
use Phlexible\Bundle\TreeBundle\Tree\TreeIterator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generate mapped fields command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GenerateMappedFieldsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('tree:generate-mapped-fields')
            ->setDescription('Generate mapped fields.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $treeManager = $this->getContainer()->get('phlexible_tree.tree_manager');
        $fieldMapper = $this->getContainer()->get('phlexible_tree.field_mapper');
        $versionStrategy = $this->getContainer()->get('phlexible_tree.mediator.preview_version_strategy');
        $elementMediator = $this->getContainer()->get('phlexible_tree.mediator.element');
        $elementMediator->setVersionStrategy($versionStrategy);

        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $repo = $em->getRepository('PhlexibleTreeBundle:NodeMappedField');

        $languages = array('de');

        foreach ($treeManager->getAll() as $tree) {
            $tree->setDefaultLanguage('de');

            $rii = new \RecursiveIteratorIterator(new TreeIterator($tree), \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($rii as $node) {
                $versions = $node->getContentVersions();
                if (!$versions) {
                    $output->writeln("Skipping {$node->getId()}, no versions");
                    continue;
                }

                foreach ($versions as $version) {
                    foreach ($languages as $language) {
                        $extractedFields = $fieldMapper->extract($node, $language, $version);

                        $mappedField = $repo->findOneBy(array('nodeId' => $node->getId(), 'language' => $language, 'version' => $version));
                        if (!$mappedField) {
                            $mappedField = new NodeMappedField();
                            $mappedField
                                ->setNodeId($node->getId())
                                ->setLanguage($language)
                                ->setVersion($version);

                            $em->persist($mappedField);
                        }

                        $mappedField->setMapping($extractedFields);
                    }
                }

                $em->flush();
            }
        }

        return 0;
    }
}

