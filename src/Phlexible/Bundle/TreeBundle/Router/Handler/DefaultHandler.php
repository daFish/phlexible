<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Router\Handler;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\SiterootBundle\Entity\Url;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeManager;
use Phlexible\Bundle\TreeBundle\ContentTree\XmlContentTree;
use Phlexible\Bundle\TreeBundle\Tree\Node\TreeNodeInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
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
     * @var ContentTreeManager
     */
    private $contentTreeManager;

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
     * @var ElementService
     */
    private $elementService;

    /**
     * @param LoggerInterface    $logger
     * @param ContentTreeManager $treeManager
     * @param ElementService     $elementService
     * @param string             $languages
     * @param string             $defaultLanguage
     */
    public function __construct(LoggerInterface $logger,
                                ContentTreeManager $treeManager,
                                ElementService $elementService,
                                $languages,
                                $defaultLanguage)
    {
        $this->logger = $logger;
        $this->contentTreeManager = $treeManager;
        $this->languages = explode(',', $languages);
        $this->defaultLanguage = $defaultLanguage;
        $this->elementService = $elementService;
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
        $treeNode = $name;
        $language = 'de';//$parameters['language'];
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
                $scheme = $treeNode->getPage($this->versionStrategy->getVersion($treeNode,
                    $language))['https'] ? 'https' : 'http';
            }

            $hostname = $this->generateHostname($treeNode, $language);

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
        // remove pathPrefix (debug, preview), strip trailing slash
        $tree = $this->findTree($request);

        if (null === $tree) {
            $msg = 'No matching siteroot url found, and no fallback siteroot url provided.';
            throw new \Exception($msg);
        }

        $parameters = $this->matchIdentifiers($request, $tree);

        if (0 && !$siterootUrl->isDefault()) {
            $siterootUrl = $siterootUrl->getSiteroot()->getDefaultUrl($request->attributes->get('language'));
            // forward?
        }

        //$request->attributes->set('siterootUrl', $siterootUrl);

        return $parameters;

        return array(
            'siterootUrl' => $siterootUrl,
            'identifiers' => $identifiers,
            'parameters'  => $parameters,
        );
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
        $default = null;
        foreach ($this->contentTreeManager->findAll() as $tree) {
            foreach ($tree->getUrls() as $siterootUrl) {
                if ($siterootUrl->getHostname() === $request->getHttpHost()) {
                    $request->attributes->set('siterootUrl', $siterootUrl);

                    return $tree;
                }
                if ($tree->isDefaultSiteroot()) {
                    $default = array('tree' => $tree, 'siterootUrl' => $siterootUrl);
                }
            }
        }

        if ($default) {
            $request->attributes->set('siterootUrl', $default['siterootUrl']);

            return $default['tree'];
        }

        return null;
    }

    /**
     * Match identifieres (tid, language, ...)
     *
     * @param Request        $request
     * @param XmlContentTree $tree
     *
     * @return array
     */
    protected function matchIdentifiers(Request $request, XmlContentTree $tree)
    {
        $match = array();
        $path = $request->getPathInfo();

        /* @var $siterootUrl Url */
        $siterootUrl = $request->attributes->get('siterootUrl');

        $attributes = array();

        if (!strlen($path)) {
            // no path, use siteroot defaults
            $language = $siterootUrl->getLanguage();
            $tid = $siterootUrl->getTarget();

            $this->logger->debug('Using TID from siteroot url target: ' . $tid . ':' . $language);
        } elseif (preg_match('#^/(\w\w)/(.+)\.(\d+)\.html#', $path, $match)) {
            // match found
            $language = $match[1];
            //$path     = $match[2];
            $tid = $match[3];
        } elseif (preg_match('#^/preview/(\w\w)/(.+)\.(\d+)\.html#', $path, $match)) {
            // match found
            $language = $match[1];
            //$path     = $match[2];
            $tid = $match[3];
        } else {
            $language = null;
            $tid = null;
            $language = $siterootUrl->getLanguage();
            $tid = $siterootUrl->getTarget();
        }

        if ($language === null) {
            if (function_exists('http_negotiate_language')) {
                array_unshift($this->languages, $this->defaultLanguage);

                $language = http_negotiate_language($this->languages);
                $this->logger->debug('Using negotiated language: ' . $language);
            } else {
                $language = $this->defaultLanguage;
                $this->logger->debug('Using default language: ' . $language);
            }
        }

        if ($tid) {
            $request->attributes->set('tid', $tid);

            $treeNode = $tree->get($tid);
            /*
            if ($siterootUrl->getSiteroot()->getId() === $tree->getSiteRootId()) {
                // only set on valid siteroot
                $treeNode = $tree->get($tid);
            }
            */

            $attributes['_route'] = $path;
            $attributes['_route_object'] = $treeNode;
            $attributes['_content'] = $treeNode;
            $attributes['_controller'] = 'PhlexibleFrontendBundle:Online:index';
        }

        $request->attributes->set('_locale', $language);

        return $attributes;
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
     * Generate hostname
     *
     * @param TreeNodeInterface $node
     *
     * @return string
     */
    protected function generateHostname(TreeNodeInterface $node)
    {
        $siteroot = $this->siterootRepository->find($node->getSiteRootId());
        $siterootUrl = $siteroot->getDefaultUrl();

        return $siterootUrl->getHostname();
    }

    /**
     * Generate path
     *
     * @param TreeNodeInterface $node
     * @param string            $language
     *
     * @return string
     */
    protected function generatePath(TreeNodeInterface $node, $language)
    {
        $tree = $node->getTree();

        // we reverse the order to determine if this leaf is no full element
        // if the is the case we don't have to continue, only full elements
        // have paths
        $pathNodes = array_reverse($tree->getPath($node));

        $parts = array();

        foreach ($pathNodes as $pathNode) {
            $parts[] = $pathNode->getSlug($language);
        }

        if (!count($parts)) {
            return '';
        }

        $path = '/' . implode('/', array_reverse($parts));

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

    /**
     * @param string $input
     *
     * @return string
     */
    protected function _transliterate($input)
    {
        $chars = array(
            "ȥ" => "z",
            "ẓ" => "z",
            "ẕ" => "z",
            "ƶ" => "z",
            "¨" => "",
            "'" => "-",
            "’" => "-",
            "΅" => "",
            "΄" => "",
            "ͺ" => "",
            "–" => "-",
            "᾿" => "",
            "῾" => "",
            "῍" => "",
            "῝" => "",
            "῎" => "",
            "῞" => "",
            "῏" => "",
            "῟" => "",
            "῀" => "",
            "῁" => "",
            "΅" => "",
            "`" => "",
            "῭" => "",
            "᾽" => "",
            "ἀ" => "a",
            "ἁ" => "a",
            "ἂ" => "a",
            "ἃ" => "a",
            "ἄ" => "a",
            "ἅ" => "a",
            "ἆ" => "a",
            "ἇ" => "a",
            "ᾀ" => "a",
            "ᾁ" => "a",
            "ᾂ" => "a",
            "ᾃ" => "a",
            "ᾄ" => "a",
            "ᾅ" => "a",
            "ᾆ" => "a",
            "ᾇ" => "a",
            "ὰ" => "a",
            "ά" => "a",
            "ᾰ" => "a",
            "ᾱ" => "a",
            "ᾲ" => "a",
            "ᾳ" => "a",
            "ᾴ" => "a",
            "ᾶ" => "a",
            "ᾷ" => "a",
            "ა" => "a",
            "Ἀ" => "A",
            "Ἁ" => "A",
            "Ἂ" => "A",
            "Ἃ" => "A",
            "Ἄ" => "A",
            "Ἅ" => "A",
            "Ἆ" => "A",
            "Ἇ" => "A",
            "ᾈ" => "A",
            "ᾉ" => "A",
            "ᾊ" => "A",
            "ᾋ" => "A",
            "ᾌ" => "A",
            "ᾍ" => "A",
            "ᾎ" => "A",
            "ᾏ" => "A",
            "Ᾰ" => "A",
            "Ᾱ" => "A",
            "Ὰ" => "A",
            "Ά" => "A",
            "ᾼ" => "A",
            "ä" => "a",
            "ä" => "a",
            "Ä" => "A",
            "Ä" => "A",
            "ą" => "a",
            "Ä" => "Ae",
            "Æ" => "A",
            "æ" => "ae",
            "Æ" => "AE",
            "ბ" => "b",
            "ჩ" => "ch",
            "ჭ" => "ch",
            "დ" => "d",
            "ð" => "d",
            "Ð" => "D",
            "ძ" => "dz",
            "ἐ" => "e",
            "ἑ" => "e",
            "ἒ" => "e",
            "ἓ" => "e",
            "ἔ" => "e",
            "ἕ" => "e",
            "ὲ" => "e",
            "έ" => "e",
            "ე" => "e",
            "Ἐ" => "E",
            "Ἑ" => "E",
            "Ἒ" => "E",
            "Ἓ" => "E",
            "Ἔ" => "E",
            "Ἕ" => "E",
            "Έ" => "E",
            "Ὲ" => "E",
            "გ" => "g",
            "ღ" => "gh",
            "ჰ" => "h",
            "Ħ" => "H",
            "ἠ" => "i",
            "ἡ" => "i",
            "ἢ" => "i",
            "ἣ" => "i",
            "ἤ" => "i",
            "ἥ" => "i",
            "ἦ" => "i",
            "ἧ" => "i",
            "ᾐ" => "i",
            "ᾑ" => "i",
            "ᾒ" => "i",
            "ᾓ" => "i",
            "ᾔ" => "i",
            "ᾕ" => "i",
            "ᾖ" => "i",
            "ᾗ" => "i",
            "ὴ" => "i",
            "ή" => "i",
            "ῂ" => "i",
            "ῃ" => "i",
            "ῄ" => "i",
            "ῆ" => "i",
            "ῇ" => "i",
            "ἰ" => "i",
            "ἱ" => "i",
            "ἲ" => "i",
            "ἳ" => "i",
            "ἴ" => "i",
            "ἵ" => "i",
            "ἶ" => "i",
            "ἷ" => "i",
            "ὶ" => "i",
            "ί" => "i",
            "ῐ" => "i",
            "ῑ" => "i",
            "ῒ" => "i",
            "ΐ" => "i",
            "ῖ" => "i",
            "ῗ" => "i",
            "ი" => "i",
            "Ἠ" => "I",
            "Ἡ" => "I",
            "Ἢ" => "I",
            "Ἣ" => "I",
            "Ἤ" => "I",
            "Ἥ" => "I",
            "Ἦ" => "I",
            "Ἧ" => "I",
            "ᾘ" => "I",
            "ᾙ" => "I",
            "ᾚ" => "I",
            "ᾛ" => "I",
            "ᾜ" => "I",
            "ᾝ" => "I",
            "ᾞ" => "I",
            "ᾟ" => "I",
            "Ὴ" => "I",
            "Ή" => "I",
            "ῌ" => "I",
            "Ἰ" => "I",
            "Ἱ" => "I",
            "Ἲ" => "I",
            "Ἳ" => "I",
            "Ἴ" => "I",
            "Ἵ" => "I",
            "Ἶ" => "I",
            "Ἷ" => "I",
            "Ῐ" => "I",
            "Ῑ" => "I",
            "Ὶ" => "I",
            "Ί" => "I",
            "ĳ" => "ij",
            "Ĳ" => "IJ",
            "ჯ" => "j",
            "კ" => "k",
            "ქ" => "k",
            "ხ" => "kh",
            "ĸ" => "k",
            "ლ" => "l",
            "Ĺ" => "K",
            "Ľ" => "K",
            "Ŀ" => "K",
            "Ļ" => "K",
            "მ" => "m",
            "ნ" => "n",
            "ὀ" => "o",
            "ὁ" => "o",
            "ὂ" => "o",
            "ὃ" => "o",
            "ὄ" => "o",
            "ὅ" => "o",
            "ὸ" => "o",
            "ό" => "o",
            "ὠ" => "o",
            "ὡ" => "o",
            "ὢ" => "o",
            "ὣ" => "o",
            "ὤ" => "o",
            "ὥ" => "o",
            "ὦ" => "o",
            "ὧ" => "o",
            "ᾠ" => "o",
            "ᾡ" => "o",
            "ᾢ" => "o",
            "ᾣ" => "o",
            "ᾤ" => "o",
            "ᾥ" => "o",
            "ᾦ" => "o",
            "ᾧ" => "o",
            "ὼ" => "o",
            "ώ" => "o",
            "ῲ" => "o",
            "ῳ" => "o",
            "ῴ" => "o",
            "ῶ" => "o",
            "ῷ" => "o",
            "ო" => "o",
            "Ὀ" => "O",
            "Ὁ" => "O",
            "Ὂ" => "O",
            "Ὃ" => "O",
            "Ὄ" => "O",
            "Ὅ" => "O",
            "Ὸ" => "O",
            "Ό" => "O",
            "Ὠ" => "O",
            "Ὡ" => "O",
            "Ὢ" => "O",
            "Ὣ" => "O",
            "Ὤ" => "O",
            "Ὥ" => "O",
            "Ὦ" => "O",
            "Ὧ" => "O",
            "ᾨ" => "O",
            "ᾩ" => "O",
            "ᾪ" => "O",
            "ᾫ" => "O",
            "ᾬ" => "O",
            "ᾭ" => "O",
            "ᾮ" => "O",
            "ᾯ" => "O",
            "Ὼ" => "O",
            "Ώ" => "O",
            "ῼ" => "O",
            "ö" => "o",
            "ö" => "o",
            "Ö" => "O",
            "Ö" => "O",
            "Ő" => "O",
            "ø" => "o",
            "Ø" => "O",
            "ö" => "oe",
            "Ö" => "Oe",
            "პ" => "p",
            "ფ" => "p",
            "ყ" => "q",
            "ῤ" => "r",
            "ῥ" => "r",
            "რ" => "r",
            "Ῥ" => "R",
            "ŕ" => "r",
            "ř" => "r",
            "ŗ" => "r",
            "ს" => "s",
            "შ" => "sh",
            "ſ" => "ss",
            "თ" => "t",
            "ტ" => "t",
            "ც" => "ts",
            "წ" => "ts",
            "უ" => "u",
            "ü" => "u",
            "ü" => "u",
            "Ü" => "U",
            "Ü" => "Ue",
            "ვ" => "v",
            "ὐ" => "y",
            "ὑ" => "y",
            "ὒ" => "y",
            "ὓ" => "y",
            "ὔ" => "y",
            "ὕ" => "y",
            "ὖ" => "y",
            "ὗ" => "y",
            "ὺ" => "y",
            "ύ" => "y",
            "ῠ" => "y",
            "ῡ" => "y",
            "ῢ" => "y",
            "ΰ" => "y",
            "ῦ" => "y",
            "ῧ" => "y",
            "Ὑ" => "Y",
            "Ὓ" => "Y",
            "Ὕ" => "Y",
            "Ὗ" => "Y",
            "Ῠ" => "Y",
            "Ῡ" => "Y",
            "Ὺ" => "Y",
            "Ύ" => "Y",
            "ზ" => "z",
            "ჟ" => "zh",
            "Þ" => "TH",
            "Α" => "A",
            "α" => "a",
            "Ά" => "A",
            "ά" => "a",
            "Β" => "B",
            "β" => "b",
            "Γ" => "G",
            "γ" => "g",
            "Δ" => "D",
            "δ" => "d",
            "Ε" => "E",
            "ε" => "e",
            "Έ" => "E",
            "έ" => "e",
            "Ζ" => "Z",
            "ζ" => "z",
            "Η" => "I",
            "η" => "i",
            "Ή" => "I",
            "ή" => "i",
            "Θ" => "TH",
            "θ" => "th",
            "Ι" => "I",
            "ι" => "i",
            "Ί" => "I",
            "ί" => "i",
            "Ϊ" => "I",
            "ϊ" => "i",
            "ΐ" => "i",
            "Κ" => "K",
            "κ" => "k",
            "Λ" => "L",
            "λ" => "l",
            "Μ" => "M",
            "μ" => "m",
            "Ν" => "N",
            "ν" => "n",
            "Ξ" => "KS",
            "ξ" => "ks",
            "Ο" => "O",
            "ο" => "o",
            "Ό" => "O",
            "ό" => "o",
            "Π" => "P",
            "π" => "p",
            "ρ" => "r",
            "Ρ" => "R",
            "Σ" => "S",
            "σ" => "s",
            "ς" => "s",
            "Τ" => "T",
            "τ" => "t",
            "Υ" => "Y",
            "υ" => "y",
            "Ύ" => "Y",
            "ύ" => "y",
            "Ϋ" => "Y",
            "ϋ" => "y",
            "ΰ" => "y",
            "Φ" => "F",
            "φ" => "f",
            "Χ" => "X",
            "χ" => "x",
            "Ψ" => "PS",
            "ψ" => "ps",
            "Ω" => "O",
            "ω" => "o",
            "Ώ" => "O",
            "ώ" => "o",
            "а" => "A",
            "А" => "A",
            "б" => "B",
            "Б" => "B",
            "в" => "V",
            "В" => "V",
            "г" => "G",
            "Г" => "G",
            "д" => "D",
            "Д" => "D",
            "е" => "E",
            "Е" => "E",
            "ё" => "E",
            "Ё" => "E",
            "ж" => "ZH",
            "Ж" => "ZH",
            "з" => "Z",
            "З" => "Z",
            "и" => "I",
            "И" => "I",
            "й" => "I",
            "Й" => "I",
            "к" => "K",
            "К" => "K",
            "л" => "L",
            "Л" => "L",
            "м" => "M",
            "М" => "M",
            "н" => "N",
            "Н" => "N",
            "о" => "O",
            "О" => "O",
            "п" => "P",
            "П" => "P",
            "р" => "R",
            "Р" => "R",
            "с" => "S",
            "С" => "S",
            "т" => "T",
            "Т" => "T",
            "у" => "U",
            "У" => "U",
            "ф" => "F",
            "Ф" => "F",
            "х" => "KH",
            "Х" => "KH",
            "ц" => "TS",
            "Ц" => "TS",
            "ч" => "CH",
            "Ч" => "CH",
            "ш" => "SH",
            "Ш" => "SH",
            "щ" => "SHCH",
            "Щ" => "SHCH",
            "ъ" => "",
            "Ъ" => "",
            "ы" => "Y",
            "Ы" => "Y",
            "ь" => "",
            "Ь" => "",
            "э" => "E",
            "Э" => "E",
            "ю" => "YU",
            "Ю" => "YU",
            "я" => "YA",
            "Я" => "YA",
            "ß" => "ss",
        );

        $output = str_replace(
            array_keys($chars),
            array_values($chars),
            $input
        );

        return $output;
    }
}
