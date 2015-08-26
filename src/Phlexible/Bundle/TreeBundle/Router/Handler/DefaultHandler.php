<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Router\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Phlexible\Bundle\SiterootBundle\Entity\Url;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeInterface;
use Phlexible\Bundle\TreeBundle\Exception\NoSiterootUrlFoundException;
use Phlexible\Component\Node\Model\NodeInterface;
use Phlexible\Component\Node\Model\NodeManagerInterface;
use Phlexible\Component\Site\Site\SiteHostnameGenerator;
use Phlexible\Component\Site\Site\SiteRequestMatcher;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;

/**
 * Default handler
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DefaultHandler implements RequestMatcherInterface, UrlGeneratorInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var NodeManagerInterface
     */
    private $treeManager;

    /**
     * @var \Phlexible\Component\Site\Site\SiteRequestMatcher
     */
    private $siterootRequestMatcher;

    /**
     * @var SiteHostnameGenerator
     */
    private $siterootHostnameGenerator;

    /**
     * @var array
     */
    private $languages;

    /**
     * @var string
     */
    private $defaultLanguage;

    /**
     * @var RequestContext
     */
    private $requestContext;

    /**
     * @param LoggerInterface           $logger
     * @param EntityManagerInterface    $em
     * @param \Phlexible\Component\Node\Model\NodeManagerInterface      $treeManager
     * @param \Phlexible\Component\Site\Site\SiteRequestMatcher    $siterootRequestMatcher
     * @param SiteHostnameGenerator $siterootHostnameGenerator
     * @param string                    $languages
     * @param string                    $defaultLanguage
     */
    public function __construct(
        LoggerInterface $logger,
        EntityManagerInterface $em,
        NodeManagerInterface $treeManager,
        SiteRequestMatcher $siterootRequestMatcher,
        SiteHostnameGenerator $siterootHostnameGenerator,
        $languages,
        $defaultLanguage)
    {
        $this->logger = $logger;
        $this->em = $em;
        $this->treeManager = $treeManager;
        $this->siterootRequestMatcher = $siterootRequestMatcher;
        $this->siterootHostnameGenerator = $siterootHostnameGenerator;
        $this->languages = explode(',', $languages);
        $this->defaultLanguage = $defaultLanguage;
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(RequestContext $requestContext)
    {
        $this->requestContext = $requestContext;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->requestContext;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        /* @var $treeNode \Phlexible\Component\Node\Model\NodeInterface */
        $treeNode = $name;
        $language = isset($parameters['language']) ? $parameters['language'] : 'de';
        $encode = false;
        /*
        TreeNode $treeNode,
        $language,
        $fragment = '',
        $encode = false
        */

        $url = '';

        if ($referenceType === self::ABSOLUTE_URL) {
            $scheme = $this->requestContext->getScheme();
            if (!$scheme || $scheme === 'http') {
                $scheme = $treeNode->getAttribute('https', 'http');
            }

            $siteroot = $this->treeManager->getBySiteRootId($treeNode->getId())->getSiteroot();

            $hostname = $this->siterootHostnameGenerator->generate($siteroot, $language);

            $port = '';
            if ($scheme === 'http' && $this->requestContext->getHttpPort() !== 80) {
                $port = ':' . $this->requestContext->getHttpPort();
            }
            if ($scheme === 'https' && $this->requestContext->getHttpsPort() !== 443) {
                $port = ':' . $this->requestContext->getHttpsPort();
            }

            $url .= $scheme . '://' . $hostname . $port;
        }

        $basePath = $this->requestContext->getBaseUrl();

        $path = $this->generatePath($treeNode, $language);

        $query = '';
        if (count($parameters)) {
            $query = '?' . http_build_query($parameters, '', '&');
        }

        $fragment = '';

        $url .= $basePath . $path . $query . $fragment;

        return $encode ? htmlspecialchars($url) : $url;
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function matchRequest(Request $request)
    {
        $repo = $this->em->getRepository('PhlexibleTreeBundle:Route');
        echo $request->getPathInfo();
        $route = $repo->findBy(array('path' => $request->getPathInfo()));
        dump($route);die;

        $tree = $this->findTree($request);

        if (null === $tree) {
            $msg = 'No matching siteroot url found, and no fallback siteroot url provided.';
            throw new NoSiterootUrlFoundException($msg);
        }

        $parameters = $this->matchIdentifiers($request, $tree);

        if ($parameters === null) {
            throw new ResourceNotFoundException("bla");
        }

        if (0 && !$siterootUrl->isDefault()) {
            $siterootUrl = $siterootUrl->getSiteroot()->getDefaultUrl($request->getLocale());
            // forward?
        }

        //$request->attributes->set('siterootUrl', $siterootUrl);

        if (isset($parameters['_route_object'])) {
            $treeNode = $parameters['_route_object'];
            /* @var $treeNode \Phlexible\Component\Node\Model\NodeInterface */
            if ($cache = $treeNode->getAttribute('cache')) {
                $configuration = new Cache(array());

                if (!empty($cache['ETag'])) {
                    $configuration->setETag($cache['ETag']);
                }
                if (!empty($cache['lastModified'])) {
                    $configuration->setLastModified($cache['lastModified']);
                }
                if (!empty($cache['expires'])) {
                    $configuration->setExpires($cache['expires']);
                }
                if (!empty($cache['public'])) {
                    $configuration->setPublic($cache['public']);
                }
                if (!empty($cache['maxage'])) {
                    $configuration->setMaxAge($cache['maxage']);
                }
                if (!empty($cache['smaxage'])) {
                    $configuration->setSMaxAge($cache['smaxage']);
                }
                if (!empty($cache['vary'])) {
                    $configuration->setVary($cache['vary']);
                }

                $request->attributes->set('_cache', $configuration);
            }

            if ('true' !== $expression = $treeNode->getSecurityExpression()) {
                $configuration = new Security(array('expression' => $expression));
                $request->attributes->set('_security', $configuration);
            }
        }

        return $parameters;
    }

    /**
     * Match siteroot URL.
     *
     * @param Request $request
     *
     * @return int|null
     */
    protected function findTree(Request $request)
    {
        $siteroot = $this->siterootRequestMatcher->matchRequest($request);
        if (!$siteroot) {
            return null;
        }

        return $this->treeManager->find($siteroot->getId());
    }

    /**
     * Match identifieres (tid, language, ...)
     *
     * @param Request              $request
     * @param ContentTreeInterface $tree
     *
     * @return array
     */
    protected function matchIdentifiers(Request $request, ContentTreeInterface $tree)
    {
        $match = array();
        $path = $request->getPathInfo();
        $language = null;
        $tid = null;

        /* @var $siterootUrl Url */
        $siterootUrl = $request->attributes->get('siterootUrl');

        $attributes = array();

        if (!strlen($path) || $path === '/') {
            $language = $siterootUrl->getLanguage();
            $tid = $siterootUrl->getTarget();

            $this->logger->debug('Using TID from siteroot url target: ' . $tid . ':' . $language);
        } elseif (preg_match('#^/(\w\w)/(.+)\.(\d+)\.html#', $path, $match)) {
            // match found
            $language = $match[1];
            $tid = $match[3];
        } elseif (preg_match('#^/preview/(\w\w)/(.+)\.(\d+)\.html#', $path, $match)) {
            // match found
            $language = $match[1];
            $tid      = $match[3];
        }

        if ($language === null) {
            $language = $this->findLanguage();
        }

        if ($language) {
            $request->setLocale($language);
            $request->attributes->set('_locale', $language);
        }

        if (!$tid) {
            return null;
        }

        $request->attributes->set('tid', $tid);

        $tree->setLanguage($language);
        $treeNode = $tree->get($tid);
        if (!$treeNode) {
            return null;
        }
        /*
        if ($siterootUrl->getSiteroot()->getId() === $tree->getSiteRootId()) {
            // only set on valid siteroot
            $treeNode = $tree->get($tid);
        }
        */

        $attributes['_route'] = $path;
        $attributes['_route_object'] = $treeNode;
        $attributes['_content'] = $treeNode;
        $attributes['_controller'] = 'PhlexibleCmsBundle:Online:index';

        return $attributes;
    }

    /**
     * @return string
     */
    private function findLanguage()
    {
        if (function_exists('http_negotiate_language')) {
            array_unshift($this->languages, $this->defaultLanguage);

            $language = http_negotiate_language($this->languages);
            $this->logger->debug('Using negotiated language: ' . $language);
        } else {
            $language = $this->defaultLanguage;
            $this->logger->debug('Using default language: ' . $language);
        }

        return $language;
    }

    /**
     * Match parameters
     *
     * @param Request $request
     *
     * @return array
     */
    protected function matchParameters(Request $request)
    {
        return $request->query->all();
    }

    /**
     * Generate path
     *
     * @param \Phlexible\Component\Node\Model\NodeInterface $node
     * @param string            $language
     *
     * @return string
     */
    protected function generatePath(NodeInterface $node, $language)
    {
        if ($this->requestContext->getParameter('preview')) {
            return $this->generatePreviewPath($node, $language);
        }

        $tree = $node->getTree();

        // we reverse the order to determine if this leaf is no full element
        // if the is the case we don't have to continue, only full elements
        // have paths
        $pathNodes = array_reverse($tree->getPath($node));

        $parts = array();

        foreach ($pathNodes as $pathNode) {
            if ($tree->isViewable($pathNode)) {
                $parts[] = $pathNode->getSlug($language);
            }
        }

        if (!count($parts)) {
            if (!count($pathNodes)) {
                return '';
            }

            $current = $pathNodes[0];
            $parts[] = $current->getSlug($language);
        }

        $path = '/' . implode('/', array_reverse($parts));

        /*
        // transliterate to ascii
        $path = $this->_transliterate($path);
        // to lowercase
        $path = mb_strtolower($path, 'UTF-8');
        // replace non ascii chars with underscore
        $path = preg_replace('#[^a-z0-9_/]+#', '_', $path);
        // replace duplicate underscores with single underscore
        $path = preg_replace('#_{2,}#', '_', $path);
        // remove leading underscores in path fragments
        $path = preg_replace('#(.*)/_+(.*)$#', '$1/$2', $path);
        // remove trailing underscores in path fragments
        $path = preg_replace('#(.*)_+/(.*)$#', '$1/$2', $path);
        // remove trailing underscores
        $path = preg_replace('#_+$#', '', $path);
        */

        // add language
        $path = '/' . $language . $path;

        /*
        if ($this->hasContext())
        {
            $country = $this->_context->getCountry();

            if (Makeweb_Elements_Context::NO_COUNTRY === $country)
            {
                $container = MWF_Registry::getContainer();

                $country = $container->getParam(':phlexible_element.context.default_country');

                if (!strlen($country))
                {
                    $country = Makeweb_Elements_Context::GLOBAL_COUNTRY;
                }
            }

            $cleartext = '/' . $country . $cleartext;
        }
        */

        // add tid and postfix
        $path .= '.' . $node->getId() . '.html';

        return $path;
    }

    protected function generatePreviewPath(NodeInterface $node, $language)
    {
        return "/admin/frontend/preview/$language/{$node->getId()}";
    }
}
