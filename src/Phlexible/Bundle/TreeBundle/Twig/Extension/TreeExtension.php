<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Twig\Extension;

use Phlexible\Bundle\TreeBundle\Model\TreeManagerInterface;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Bundle\TreeBundle\Node\ReferenceNodeContext;
use Phlexible\Bundle\TreeBundle\Pattern\PatternResolver;
use Phlexible\Component\Site\Domain\Site;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Twig tree extension.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeExtension extends \Twig_Extension
{
    /**
     * @var TreeManagerInterface
     */
    private $treeManager;

    /**
     * @var PatternResolver
     */
    private $patternResolver;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var array
     */
    private $navigations = array();

    /**
     * @param TreeManagerInterface          $treeManager
     * @param PatternResolver               $patternResolver
     * @param RequestStack                  $requestStack
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TokenStorageInterface         $tokenStorage
     */
    public function __construct(
        TreeManagerInterface $treeManager,
        PatternResolver $patternResolver,
        RequestStack $requestStack,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage
    ) {
        $this->treeManager = $treeManager;
        $this->patternResolver = $patternResolver;
        $this->requestStack = $requestStack;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('navigation', array($this, 'navigation')),
            new \Twig_SimpleFunction('tree_node', array($this, 'treeNode')),
            new \Twig_SimpleFunction('node_granted', array($this, 'nodeGranted')),
            new \Twig_SimpleFunction('page_title', array($this, 'pageTitle')),
            new \Twig_SimpleFunction('page_title_pattern', array($this, 'pageTitlePattern')),
        );
    }

    /**
     * @param string $name
     *
     * @return NodeContext|null
     */
    public function navigation($name)
    {
        if (!isset($this->navigations[$name])) {
            $request = $this->requestStack->getCurrentRequest();

            if (!$request->attributes->has('siteroot')) {
                throw new \InvalidArgumentException('Need a siteroot request attribute for navigations.');
            }
            if (!$request->attributes->has('node')) {
                throw new \InvalidArgumentException('Need a node request attribute for navigations');
            }

            $siteroot = $request->attributes->get('siteroot');
            $currentNode = $request->attributes->get('node');

            $navigations = $siteroot->getNavigations();

            if (!isset($navigations[$name])) {
                return $this->navigations[$name] = null;
            }

            if (empty($navigations[$name]['nodeId'])) {
                throw new \Exception('No start node ID');
            }

            $navigation = $navigations[$name];

            $node = $currentNode->getTree()->get($navigation['nodeId']);

            $this->navigations[$name] = ReferenceNodeContext::fromNodeContext(
                $node,
                $currentNode,
                $navigation['maxDepth']
            );
        }

        return $this->navigations[$name];
    }

    /**
     * @param string $nodeId
     *
     * @return NodeContext
     */
    public function treeNode($nodeId)
    {
        $tree = $this->treeManager->getByNodeId($nodeId);
        if (!$tree) {
            return null;
        }

        return $tree->get($nodeId);
    }

    /**
     * @param NodeContext $node
     *
     * @return bool
     */
    public function nodeGranted($node)
    {
        if ($this->tokenStorage->getToken() === null) {
            return false;
        }

        /* @var $nodes NodeContext[] */

        if ($node instanceof NodeContext) {
            $nodes = array($node->getNode());
        } elseif (is_array($node)) {
            $nodes = $node;
        } else {
            $nodes = array($node);
        }

        foreach ($nodes as $node) {
            if ($node instanceof NodeContext) {
                $node = $node->getNode();
            }

            if ($this->authorizationChecker->isGranted(new Expression($node->getSecurityExpression()))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string      $name
     * @param string      $language
     * @param NodeContext $node
     * @param Site        $siteroot
     *
     * @return string
     */
    public function pageTitle($name = 'default', $language = null, NodeContext $node = null, Site $siteroot = null)
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($siteroot === null) {
            $siteroot = $request->attributes->get('siteroot');
        }

        if ($node === null) {
            $node = $request->get('node');
        }

        if ($language === null) {
            $language = $request->getLocale();
        }

        $title = $this->patternResolver->replace($name, $siteroot, $node, $language);

        return $title;
    }

    /**
     * @param string      $pattern
     * @param string      $language
     * @param NodeContext $node
     * @param Site        $siteroot
     *
     * @return string
     */
    public function pageTitlePattern($pattern, $language = null, NodeContext $node = null, Site $siteroot = null)
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($siteroot === null) {
            $siteroot = $request->attributes->get('siterootUrl')->getSiteroot();
        }

        if ($node === null) {
            $node = $request->get('contentDocument');
        }

        if ($language === null) {
            $language = $request->getLocale();
        }

        $title = $this->patternResolver->replacePattern($pattern, $siteroot, $node, $language);

        return $title;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'phlexible_tree';
    }
}
