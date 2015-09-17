<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\ElementProxy\Generator;

use Phlexible\Component\ElementProxy\Distiller\DistilledFieldNode;
use Phlexible\Component\ElementProxy\Distiller\DistilledNodeCollection;
use Phlexible\Component\ElementProxy\Distiller\Distiller;
use Phlexible\Component\ElementProxy\Distiller\HasChildNodesInterface;
use Phlexible\Component\Elementtype\Domain\Elementtype;

/**
 * Php class generator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ProxyGenerator
{
    /**
     * @var Distiller
     */
    private $distiller;

    /**
     * @var DefinitionWriter
     */
    private $writer;

    /**
     * @var string
     */
    private $namespacePrefix;

    /**
     * @var string
     */
    private $referenceNamespacePrefix;

    /**
     * @param Distiller        $distiller
     * @param DefinitionWriter $writer
     * @param string           $namespacePrefix
     */
    public function __construct(Distiller $distiller, DefinitionWriter $writer, $namespacePrefix = 'Phlexible\\Element\\__CG__\\')
    {
        $this->distiller = $distiller;
        $this->writer = $writer;
        $this->namespacePrefix = $namespacePrefix;
        $this->referenceNamespacePrefix = $namespacePrefix . 'Reference';
    }

    /**
     * @return mixed
     */
    public function getManagerFile()
    {
        return $this->writer->getManagerFile();
    }

    /**
     * @param Elementtype[] $elementtypes
     *
     * @return string
     */
    public function generate(array $elementtypes)
    {
        $definitions = array();
        foreach ($elementtypes as $elementtype) {
            if ($elementtype->getType() !== 'full' && $elementtype->getType() !== 'part' && $elementtype->getType() !== 'structure') {
                continue;
            }

            $nodes = $this->distiller->distill($elementtype);

            $classname = $this->normalizeName($elementtype->getName());
            $namespace = $this->namespacePrefix . $classname;

            $definitions[] = $this->makeMainClass(
                $namespace,
                $classname,
                $nodes,
                $elementtype,
                $this->generateSubClasses($namespace, $nodes)
            );
        }

        return $this->writer->write($definitions, $this->namespacePrefix);
    }

    /**
     * @param string                  $namespace
     * @param string                  $classname
     * @param DistilledNodeCollection $nodes
     * @param Elementtype             $elementtype
     * @param array                   $children
     *
     * @return MainClassDefinition
     */
    private function makeMainClass($namespace, $classname, DistilledNodeCollection $nodes, Elementtype $elementtype, array $children)
    {
        $values = $this->extractValues($nodes);
        $class = new MainClassDefinition($classname, $namespace, $values, $children['classes'], $children['collections'], $elementtype->getId(), $elementtype->getRevision(), $elementtype->getName());

        return $class;
    }

    /**
     * @param string                   $namespace
     * @param string                   $classname
     * @param DistilledNodeCollection  $nodes
     * @param string                   $nodeName
     * @param string                   $dsId
     * @param array                    $children
     *
     * @return StructureClassDefinition
     */
    private function makeStructureClass($namespace, $classname, DistilledNodeCollection $nodes, $nodeName, $dsId, array $children)
    {
        $values = $this->extractValues($nodes);
        $class = new StructureClassDefinition($classname, $namespace, $values, $children['classes'], $children['collections'], $nodeName, $dsId);

        return $class;
    }

    /**
     * @param string                   $namespace
     * @param string                   $classname
     * @param DistilledNodeCollection  $nodes
     * @param string                   $nodeName
     * @param string                   $dsId
     * @param array                    $children
     *
     * @return CollectionStructureClassDefinition
     */
    private function makeCollectionStructureClass($namespace, $classname, DistilledNodeCollection $nodes, $nodeName, $dsId, array $children)
    {
        $values = $this->extractValues($nodes);
        $class = new CollectionStructureClassDefinition($classname, $namespace, $values, $children, $nodeName, $dsId);

        return $class;
    }

    /**
     * @param DistilledNodeCollection $nodes
     *
     * @return array
     */
    private function extractValues(DistilledNodeCollection $nodes)
    {
        $nodes = $nodes->filter(
            function($node) {
                return !$node instanceof HasChildNodesInterface;
            }
        );

        $values = array();
        foreach ($nodes->all() as $node) {
            /* @var $node DistilledFieldNode */

            $values[] = new ValueDefinition(
                lcfirst($this->toCamelCase($node->getName())),
                $node->getName(),
                $node->getDsId(),
                $node->getType(),
                $node->getDataType()
            );
        }

        return $values;
    }

    /**
     * @param string                  $namespace
     * @param DistilledNodeCollection $nodes
     *
     * @return array
     */
    private function generateSubClasses($namespace, DistilledNodeCollection $nodes)
    {
        $nodes = $nodes->filter(
            function($node) {
                return $node instanceof HasChildNodesInterface;
            }
        );

        $data = array('classes' => array(), 'collections' => array());
        $collectionClasses = array();

        foreach ($nodes->all() as $node) {
            $parent = $node->getParentNode();
            if ($parent->getType() === 'referenceroot') {
                $parent = $parent->getParentNode();
                if ($parent->getType() === 'reference') {
                    $parent = $parent->getParentNode();
                }
            }
            $collectionName = $parent->getName();
            $normalizedCollectionName = $this->normalizeName($collectionName);

            $nodeName = $this->normalizeName($node->getName());

            $collectionNamespace = !$node->isReferenced() ? $namespace : $this->referenceNamespacePrefix;

            if ($node->isRepeatable()) {
                $collectionClassname = !$node->isReferenced() ? $normalizedCollectionName . $nodeName . 'Structure' : $nodeName . 'Structure';

                $collectionClasses[$collectionName][] = $this->makeCollectionStructureClass(
                    $collectionNamespace,
                    $collectionClassname,
                    $node->getChildNodes(),
                    $node->getName(),
                    $node->getDsId(),
                    $this->generateSubClasses($collectionNamespace, $node->getChildNodes())
                );
            } else {
                $collectionClassname = !$node->isReferenced() ? $nodeName : $nodeName;

                $data['classes'][] = $this->makeStructureClass(
                    $collectionNamespace,
                    $collectionClassname,
                    $node->getChildNodes(),
                    $node->getName(),
                    $node->getDsId(),
                    $this->generateSubClasses($collectionNamespace, $node->getChildNodes())
                );
            }
        }

        foreach ($collectionClasses as $collectionName => $classes) {
            if (!count($classes)) {
                continue;
            }
            $data['collections'][$collectionName] = new CollectionDefinition(
                ucfirst($this->normalizeName($collectionName)) . 'Collection',
                $namespace,
                lcfirst($this->normalizeName($collectionName)),
                $collectionName,
                $classes
            );
        }

        return $data;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function normalizeName($name)
    {
        //$name = strtolower($name);
        $name = str_replace(' ', '_', $name);
        $name = str_replace('-', '_', $name);

        return $this->toCamelCase($name);
    }

    /**
     * @param string $str
     * @param bool   $capitaliseFirstChar
     *
     * @return string
     */
    private function toCamelCase($str, $capitaliseFirstChar = true)
    {
        if ($capitaliseFirstChar) {
            $str = ucfirst($str);
        }

        $func = function ($str) {
            return strtoupper($str[1]);
        };

        $str = preg_replace_callback('/_([a-zA-Z])/', $func, $str);

        return $str;
    }
}
