<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Command;

use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Test command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TestCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('element:test-transform')
            ->setDescription('test.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $elementManager = $this->getContainer()->get('phlexible_element.element_manager');
        $elementVersionManager = $this->getContainer()->get('phlexible_element.element_version_manager');
        $elementStructureManager = $this->getContainer()->get('phlexible_element.element_structure_manager');
        $this->getContainer()->get('doctrine.dbal.default_connection')->getConfiguration()->setSQLLogger(null);
        $doctrine = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $elements = $elementManager->findBy(array());

        $i = 0;
        $needFlush = false;
        foreach ($elements as $element) {
            //if ($element->getEid() !== 67) continue;
            $elementVersions = $elementVersionManager->findBy(array('element' => $element));
            foreach ($elementVersions as $elementVersion) {
                $output->writeln($i . ') ' . $element->getEid() . ' ' . $elementVersion->getVersion());

                $structure = $elementStructureManager->find($elementVersion);

                $content = $this->structureToJson($structure, $element->getEid(), $elementVersion->getVersion());

                $elementVersion->setContent($content);
                $elementVersionManager->updateElementVersion($elementVersion, false);
                $i++;

                if ($i % 100 === 0) {
                    $needFlush = true;
                }
            }
            if ($needFlush) {
                $output->writeln('flush');
                $doctrine->flush();
                $doctrine->clear();
                $needFlush = false;
            }
        }

        $doctrine->flush();
        $doctrine->clear();

        return 0;
    }

    private function structureToJson(ElementStructure $structure, $eid = null, $version = null)
    {
        if (!$structure->getId()) {
            return null;
        }

        $values = array();
        foreach ($structure->getLanguages() as $language) {
            foreach ($structure->getValues($language) as $value) {
                $values[$value->getDsId()][$language] = $value->getValue();
            }
        }

        $children = array();
        foreach ($structure->getStructures() as $subStructure) {
            $child = $this->structureToJson($subStructure);
            if ($child) {
                $children[] = $child;
            }
        }

        if ($structure->getType() === 'root') {
            $result = array(
                'id' => $eid,
                'version' => $version,
                'parent' => null,
                'values' => $values,
                'children' => $children,
            );
        } else {
            $result = array(
                'id' => (int) $structure->getId(),
                'dsId' => $structure->getDsId(),
                'parent' => $structure->getParentName(),
                'values' => $values,
                'children' => $children,
            );
        }

        return $result;
    }
}

