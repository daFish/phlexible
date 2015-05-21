<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Elementtype\File\Loader;

use FluentDOM\Document;
use Phlexible\Bundle\GuiBundle\Locator\PatternResourceLocator;
use Phlexible\Component\Elementtype\File\Parser\XmlParser;
use Phlexible\Component\Elementtype\Model\Elementtype;

/**
 * XML loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlLoader implements LoaderInterface
{
    /**
     * @var PatternResourceLocator
     */
    private $locator;

    /**
     * @var XmlParser
     */
    private $parser;

    /**
     * @param PatternResourceLocator $locator
     */
    public function __construct(PatternResourceLocator $locator)
    {
        $this->locator = $locator;

        $this->parser = new XmlParser($this);
    }

    /**
     * {@inheritdoc}
     */
    public function loadAll()
    {
        $files = [];
        foreach ($this->locator->locate('*.xml', 'elementtypes', false) as $file) {
            $id = basename($file, '.xml');
            if (!isset($files[$id])) {
                $files[$id] = $file;
            }
        }

        $elementtypes = [];
        foreach ($files as $id => $file) {
            $elementtypes[] = $this->parse($this->loadFile($file));
        }

        return $elementtypes;
    }

    /**
     * {@inheritdoc}
     */
    public function load($elementtypeId)
    {
        return $this->parse($this->loadElementtype($elementtypeId));
    }

    /**
     * @param string $elementtypeId
     *
     * @return Document
     */
    private function loadElementtype($elementtypeId)
    {
        $filename = $this->locator->locate("$elementtypeId.xml", 'elementtypes', true);

        return $this->loadFile($filename);
    }

    /**
     * {@inheritdoc}
     *
     * @return Document
     */
    private function loadFile($filename)
    {
        $dom = new Document();
        $dom->formatOutput = true;
        $dom->load($filename);

        $this->applyReferenceElementtype($dom);

        return $dom;
    }

    /**
     * @param Document $dom
     */
    private function applyReferenceElementtype(Document $dom)
    {
        foreach ($dom->xpath()->evaluate('//node[@referenceElementtypeId]') as $node) {
            /* @var $node \FluentDOM\Element */
            $referenceElementtypeId = $node->getAttribute('referenceElementtypeId');
            $referenceDom = $this->loadElementtype($referenceElementtypeId);
            foreach ($referenceDom->documentElement->evaluate('structure[1]/node') as $referenceNode) {
                $node->appendElement('references')->append($referenceNode);
            }
        }
    }

    /**
     * @param Document $dom
     *
     * @return Elementtype
     */
    private function parse(Document $dom)
    {
        return $this->parser->parse($dom);
    }
}