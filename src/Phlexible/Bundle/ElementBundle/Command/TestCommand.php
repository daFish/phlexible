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
        $elementService = $this->getContainer()->get('phlexible_element.element_service');
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

                $structure = $elementService->findElementStructure($elementVersion);

                $content = $this->structureToJson($structure);

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

    private function structureToJson(ElementStructure $structure)
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

        $result = array(
            'id' => $structure->getType() !== 'root' ? (int) $structure->getId() : null,
            'dsId' => $structure->getType() !== 'root' ? $structure->getDsId() : null,
            'parent' => $structure->getParentName(),
            'values' => $values,
            'children' => $children,
        );

        return $result;
    }
}

