<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Site\File\Dumper;

use FluentDOM\Document;
use Phlexible\Component\Site\Domain\Site;

/**
 * XML dumper
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlDumper
{
    /**
     * {@inheritdoc}
     */
    public function dump(Site $site)
    {
        $dom = new Document();
        $dom->formatOutput = true;

        $rootElement = $dom->appendElement(
            'site',
            '',
            array(
                'id'          => $site->getId(),
                'hostname'    => $site->getHostname(),
                'created_at'  => $site->getCreatedAt()->format('Y-m-d H:i:s'),
                'created_by'  => $site->getCreateUserId(),
                'modified_at' => $site->getModifiedAt()->format('Y-m-d H:i:s'),
                'modified_by' => $site->getModifyUserId(),
            )
        );

        if ($site->getEntryPoints()) {
            $entryPointsNode = $rootElement->appendElement('entryPoints');
            foreach ($site->getEntryPoints() as $entryPoint) {
                $entryPointsNode->appendElement('entryPoint', '', array(
                    'name'     => $entryPoint->getName(),
                    'hostname' => $entryPoint->getHostname(),
                    'language' => $entryPoint->getLanguage(),
                    'nodeId'   => $entryPoint->getNodeId(),
                ));
            }
        }

        if ($site->getProperties()) {
            $propertiesNode = $rootElement->appendElement('properties');
            foreach ($site->getProperties() as $key => $value) {
                $propertiesNode->appendElement('property', $value, array(
                    'key' => $key,
                ));
            }
        }

        if ($site->getTitles()) {
            $titlesNode = $rootElement->appendElement('titles');
            foreach ($site->getTitles() as $language => $title) {
                $titlesNode->appendElement('title', $title, array(
                    'language' => $language,
                ));
            }
        }

        if ($site->getNavigations()) {
            $nodeAliasesNode = $rootElement->appendElement('navigations');
            foreach ($site->getNavigations() as $navigation) {
                $nodeAliasesNode->appendElement('navigation', '', array(
                    'name'     => $navigation->getName(),
                    'nodeId'   => $navigation->getNodeId(),
                    'maxDepth' => $navigation->getMaxDepth(),
                ));
            }
        }

        if ($site->getNodeAliases()) {
            $nodeAliasesNode = $rootElement->appendElement('nodeAliases');
            foreach ($site->getNodeAliases() as $nodeAlias) {
                $attributes = array(
                    'name'     => $nodeAlias->getName(),
                    'nodeId'   => $nodeAlias->getNodeId(),
                );
                if ($nodeAlias->getLanguage()) {
                    $attributes['language'] = $nodeAlias->getLanguage();
                }
                $nodeAliasesNode->appendElement('nodeAlias', '', $attributes);
            }
        }

        if ($site->getNodeConstraints()) {
            $nodeConstraintsNode = $rootElement->appendElement('nodeConstraints');
            foreach ($site->getNodeConstraints() as $nodeConstraint) {
                $nodeConstraintNode = $nodeConstraintsNode->appendElement('nodeConstraint', '', array(
                    'name'    => $nodeConstraint->getName(),
                    'allowed' => $nodeConstraint->isAllowed() ? 1 : 0,
                ));
                foreach ($nodeConstraint->getNodeTypes() as $nodeType) {
                    $nodeConstraintNode->appendElement('nodeType', $nodeType);
                }
            }
        }

        return $dom->saveXML();

    }
}