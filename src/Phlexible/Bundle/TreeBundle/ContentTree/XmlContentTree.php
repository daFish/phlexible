<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\ContentTree;

use Phlexible\Bundle\SiterootBundle\Entity\Navigation;
use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Phlexible\Bundle\SiterootBundle\Entity\Url;
use Phlexible\Bundle\TreeBundle\Entity\NodeState;
use Phlexible\Bundle\TreeBundle\Exception\InvalidArgumentException;
use Phlexible\Bundle\TreeBundle\Model\TreeIdentifier;
use Phlexible\Bundle\TreeBundle\Model\NodeInterface;
use Phlexible\Bundle\TreeBundle\Tree\TreeIterator;
use Phlexible\Component\Identifier\IdentifiableInterface;

/**
 * XML content tree
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlContentTree implements ContentTreeInterface, \IteratorAggregate, IdentifiableInterface
{
    /**
     * @var int
     */
    private $rootId;

    /**
     * @var array
     */
    private $nodes = array();

    /**
     * @var array
     */
    private $childNodes = array();

    /**
     * @var \DOMDocument
     */
    private $dom;

    /**
     * @var \DOMXPath
     */
    private $xpath;

    /**
     * @param string $filename
     */
    public function __construct($filename)
    {
        $this->dom = new \DOMDocument();
        $this->dom->recover = false;
        $this->dom->resolveExternals = false;
        $this->dom->strictErrorChecking = false;
        $this->dom->load($filename);
        $this->xpath = new \DOMXPath($this->dom);
    }

    /**
     * {@inheritdoc}
     *
     * @return TreeIterator
     */
    public function getIterator()
    {
        return new TreeIterator($this);
    }

    /**
     * {@inheritdoc}
     *
     * @return TreeIdentifier
     */
    public function getIdentifier()
    {
        return new TreeIdentifier($this->getSiterootId());
    }

    /**
     * {@inheritdoc}
     */
    public function getSiterootId()
    {
        return $this->xpath->query('/contentTree/siteroot')->item(0)->getAttribute('id');
    }

    /**
     * {@inheritdoc}
     */
    public function getSiteroot()
    {
        return $this->mapSiteroot($this->xpath->query('/contentTree/siteroot')->item(0));
    }

    /**
     * {@inheritdoc}
     */
    public function isDefaultSiteroot()
    {
        $attributes = $this->xpath->query('/contentTree/siteroot')->item(0)->attributes;
        if (!$attributes->length) {
            // TODO: false
            return true;
        }

        return (bool) $attributes->item(0)->value;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrls()
    {
        return $this->getSiteroot()->getUrls();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultUrl()
    {
        return $this->getSiteroot()->getDefaultUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function getNavigations()
    {
        return $this->getSiteroot()->getUrls();
    }

    /**
     * @param string $language
     *
     * @return array
     */
    public function getSpecialTids($language = null)
    {
        return $this->getSiteroot()->getSpecialTidsForLanguage($language);
    }

    /**
     * @param \DOMElement $element
     *
     * @return Url
     */
    private function mapSiteroot(\DOMElement $element)
    {
        $urlElements = $element->getElementsByTagName('url');
        $navigationElements = $element->getElementsByTagName('navigation');
        $specialTidElements = $element->getElementsByTagName('specialTid');

        $siteroot = new Siteroot();

        foreach ($urlElements as $urlElement) {
            /* @var $urlElement \DOMElement */
            $url = new Url();
            $url
                ->setSiteroot($siteroot)
                ->setId((bool) $urlElement->getAttribute('id'))
                ->setDefault((bool) $urlElement->getAttribute('default'))
                ->setHostname((string) $urlElement->textContent)
                ->setLanguage((bool) $urlElement->getAttribute('language'))
                ->setTarget((bool) $urlElement->getAttribute('target'));
            $siteroot->addUrl($url);
        }

        foreach ($navigationElements as $navigationElement) {
            /* @var $navigationElement \DOMElement */
            $navigation = new Navigation();
            $navigation
                ->setSiteroot($siteroot)
                ->setTitle((string) $navigationElement->getAttribute('title'))
                ->setStartTreeId((int) $navigationElement->getAttribute('startTreeId'))
                ->setMaxDepth((int) $navigationElement->getAttribute('maxDepth'));
            $siteroot->addNavigation($navigation);
        }

        $specialTids = array();
        foreach ($specialTidElements as $specialTidElement) {
            /* @var $specialTidElement \DOMElement */
            $name = $specialTidElement->getAttribute('name');
            $language = $specialTidElement->getAttribute('language') ? : null;
            $specialTids[] = array('name' => $name, 'language' => $language, 'treeId' => (int) $specialTidElement->textContent);
        }
        $siteroot->setSpecialTids($specialTids);

        return $siteroot;
    }

    /**
     * {@inheritdoc}
     */
    public function createContentTreeNodeFromTreeNode(NodeInterface $treeNode)
    {
        $contentNode = new ContentNode();
        $contentNode
            ->setContentId($treeNode->getContentId())
            ->setContentType($treeNode->getContentType())
            ->setTree($this)
            ->setParentNode($treeNode->getParentNode())
            ->setInNavigation($treeNode->getInNavigation())
            ->setSort($treeNode->getSort())
            ->setSortMode($treeNode->getSortMode())
            ->setSortDir($treeNode->getSortDir())
            ->setTitles(array('de' => 'bla', 'en' => 'blubb'));

        return $contentNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoot()
    {
        if ($this->rootId) {
            return $this->nodes[$this->rootId];
        }

        $elements = $this->xpath->query('/contentTree/tree/node[1]');

        if (!$elements->length) {
            throw new InvalidArgumentException('Root node not found.');
        }

        $element = $elements->item(0);

        return $this->mapNode($element);
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if (isset($this->nodes[$id])) {
            return $this->nodes[$id];
        }

        $elements = $this->xpath->query("//node[@id=$id]");

        if (!$elements->length) {
            throw new InvalidArgumentException("$id not found");
        }

        $element = $elements->item(0);

        return $this->mapNode($element);
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        if (isset($this->nodes[$id])) {
            return true;
        }

        if ($id instanceof NodeInterface) {
            $id = $id->getId();
        }

        return $this->get($id) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren(NodeInterface $node)
    {
        if (isset($this->childNodes[$node->getId()])) {
            return $this->childNodes[$node->getId()];
        }

        $elements = $this->xpath->query("//node[@id={$node->getId()}]/node");

        if (!$elements->length) {
            return array();
        }

        $childNodes = $this->mapNodes($elements);
        $this->childNodes[$node->getId()] = $childNodes;

        return $childNodes;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildren(NodeInterface $node)
    {
        return count($this->getChildren($node)) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(NodeInterface $node)
    {
        return $this->getParent($node);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdPath(NodeInterface $node)
    {
        return array_keys($this->getPath($node));
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(NodeInterface $node)
    {
        $elements = $this->xpath->query("//node[@id={$node->getId()}]");
        if (!$elements->length) {
            return array();
        }

        $element = $elements->item(0);
        $elementPath = $this->xpath->query($element->getNodePath());

        $path = array();
        foreach ($elementPath as $element) {
            $path[] = $this->get($element->attributes->item(0)->value);
        }

        return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function isRoot(NodeInterface $node)
    {
        return $this->getRoot()->getId() === $node->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function isChildOf(NodeInterface $childNode, NodeInterface $parentNode)
    {
        return $this->xpath->query("//node[@id={$parentNode->getId()}]//node[@id={$childNode->getId()}]")->length > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isParentOf(NodeInterface $parentNode, NodeInterface $childNode)
    {
        return $this->isChildOf($childNode, $parentNode);
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguages($node)
    {
        if ($node instanceof NodeInterface) {
            $id = $node->getId();
        } else {
            $id = $node;
        }

        $elements = $this->xpath->query("//node[@id=$id]/versions/version");

        $languages = array();
        foreach ($elements as $element) {
            $languages[] = $element->attributes->item(0)->value;
        }

        return $languages;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersions($node)
    {
        if ($node instanceof NodeInterface) {
            $id = $node->getId();
        } else {
            $id = $node;
        }

        $elements = $this->xpath->query("//node[@id=$id]/versions/version");

        $versions = array();
        foreach ($elements as $element) {
            $language = $element->attributes->item(0)->value;
            $versions[$language] = (int) $element->textContent;
        }

        return $versions;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion(NodeInterface $node, $language)
    {
        $elements = $this->xpath->query("//node[@id={$node->getId()}]/versions/version[@language=\"$language\"]");
        if (!$elements->length) {
            throw new InvalidArgumentException("language $language not found");
        }
        $version = (int) $elements->item(0)->textContent;

        return $version;
    }

    /**
     * @param \DOMNodeList $elements
     *
     * @return NodeInterface[]
     */
    private function mapNodes(\DOMNodeList $elements)
    {
        $nodes = array();

        foreach ($elements as $element) {
            $node = $this->mapNode($element);
            $nodes[$node->getId()] = $node;
        }

        return $nodes;
    }

    /**
     * @param \DOMElement $element
     *
     * @return \Phlexible\Bundle\TreeBundle\Model\NodeInterface
     */
    private function mapNode(\DOMElement $element)
    {
        $attributes = array();

        $titles = array();
        $titlesElement = $element->getElementsByTagName('titles')->item(0);
        foreach ($titlesElement->getElementsByTagName('title') as $titleNode) {
            $titles[$titleNode->getAttribute('language')] = $titleNode->textContent;
        }

        $slugs = array();
        $slugsElement = $element->getElementsByTagName('slugs')->item(0);
        foreach ($slugsElement->getElementsByTagName('slug') as $slugNode) {
            $slugs[$slugNode->getAttribute('language')] = $slugNode->textContent;
        }

        $versions = array();
        $versionsElement = $element->getElementsByTagName('versions')->item(0);
        foreach ($versionsElement->getElementsByTagName('version') as $versionNode) {
            $versions[$versionNode->getAttribute('language')] = (int) $versionNode->textContent;
        }

        $node = new ContentNode();
        $node
            ->setTree($this)
            ->setId((int) $element->getAttribute('id'))
            ->setParentNode($element->getAttribute('parentId') ? (int) $element->getAttribute('parentId') : null)
            ->setType((string) $element->getAttribute('type'))
            ->setTypeId((int) $element->getAttribute('typeId'))
            ->setAttributes($attributes)
            ->setSort((string) $element->getAttribute('sort'))
            ->setSortMode((string) $element->getAttribute('sortMode'))
            ->setSortDir((string) $element->getAttribute('sortDir'))
            ->setTitles($titles)
            ->setSlugs($slugs)
            //->setVersions($versions)
            ->setCreatedAt(new \DateTime((string) $element->getAttribute('createdAt')))
            ->setCreateUserId((string) $element->getAttribute('createUserId'));

        $this->nodes[$node->getId()] = $node;

        if ($node->getParentNode() === null) {
            $this->rootId = $node->getId();
        }

        return $node;
    }

    /**
     * @param NodeInterface $node
     *
     * @return bool
     */
    public function isInstance(NodeInterface $node)
    {
        return false;
    }

    /**
     * @param NodeInterface $node
     *
     * @return bool
     */
    public function isInstanceMaster(NodeInterface $node)
    {
        return false;
    }

    /**
     * @param NodeInterface $node
     *
     * @return NodeInterface[]
     */
    public function getInstances(NodeInterface $node)
    {
        return array();
    }

    /**
     * @param NodeInterface $node
     * @param string            $language
     *
     * @return bool
     */
    public function isPublished(NodeInterface $node, $language)
    {
        // TODO: Implement isPublished() method.
    }

    /**
     * @param NodeInterface $node
     *
     * @return array
     */
    public function getPublishedLanguages(NodeInterface $node)
    {
        // TODO: Implement getPublishedLanguages() method.
    }

    /**
     * @param NodeInterface $node
     * @param string            $language
     *
     * @return int|null
     */
    public function getPublishedVersion(NodeInterface $node, $language)
    {
        // TODO: Implement getPublishedVersion() method.
    }

    /**
     * @param NodeInterface $node
     * @param string            $language
     *
     * @return \DateTime|null
     */
    public function getPublishedAt(NodeInterface $node, $language)
    {
        // TODO: Implement getPublishedAt() method.
    }

    /**
     * @param NodeInterface $node
     *
     * @return array
     */
    public function getPublishedVersions(NodeInterface $node)
    {
        // TODO: Implement getPublishedVersions() method.
    }

    /**
     * @param NodeInterface $node
     * @param string            $language
     *
     * @return bool
     */
    public function isAsync(NodeInterface $node, $language)
    {
        // TODO: Implement isAsync() method.
    }

    /**
     * @param NodeInterface $node
     *
     * @return NodeState[]
     */
    public function findOnlineByTreeNode(NodeInterface $node)
    {
        // TODO: Implement findOnlineByTreeNode() method.
    }

    /**
     * @param NodeInterface $node
     * @param string            $language
     *
     * @return NodeState
     */
    public function findOneOnlineByTreeNodeAndLanguage(NodeInterface $node, $language)
    {
        // TODO: Implement findOneOnlineByTreeNodeAndLanguage() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getByTypeId($typeId, $type = null)
    {
        // TODO: Implement getByTypeId() method.
    }

    /**
     * {@inheritdoc}
     */
    public function hasByTypeId($typeId, $type = null)
    {
        // TODO: Implement hasByTypeId() method.
    }

    /**
     * {@inheritdoc}
     */
    public function isViewable(NodeInterface $node)
    {
        return false;
    }
}
