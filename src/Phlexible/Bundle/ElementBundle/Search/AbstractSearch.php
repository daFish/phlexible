<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Search;

use Doctrine\DBAL\Connection;
use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Icon\IconResolver;
use Phlexible\Bundle\SearchBundle\Search\SearchResult;
use Phlexible\Bundle\SearchBundle\SearchProvider\SearchProviderInterface;
use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\NodeManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Abstract element search
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class AbstractSearch implements SearchProviderInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var NodeManagerInterface
     */
    private $nodeManager;

    /**
     * @var SiterootManagerInterface
     */
    private $siterootManager;

    /**
     * @var IconResolver
     */
    private $iconResolver;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var string
     */
    private $defaultLanguage;

    /**
     * @param Connection                    $connection
     * @param ElementService                $elementService
     * @param NodeManagerInterface          $nodeManager
     * @param SiterootManagerInterface      $siterootManager
     * @param IconResolver                  $iconResolver
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param string                        $defaultLanguage
     */
    public function __construct(
        Connection $connection,
        ElementService $elementService,
        NodeManagerInterface $nodeManager,
        SiterootManagerInterface $siterootManager,
        IconResolver $iconResolver,
        AuthorizationCheckerInterface $authorizationChecker,
        $defaultLanguage)
    {
        $this->connection = $connection;
        $this->elementService = $elementService;
        $this->nodeManager = $nodeManager;
        $this->siterootManager = $siterootManager;
        $this->iconResolver = $iconResolver;
        $this->authorizationChecker = $authorizationChecker;
        $this->defaultLanguage = $defaultLanguage;
    }

    /**
     * {@inheritdoc}
     */
    public function getRole()
    {
        return 'ROLE_ELEMENTS';
    }

    /**
     * @return Connection
     */
    protected function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return string
     */
    protected function getDefaultLanguage()
    {
        return $this->defaultLanguage;
    }

    /**
     * Perform search
     *
     * @param array  $rows
     * @param string $title
     * @param string $language
     *
     * @return array
     */
    protected function doSearch(array $rows, $title, $language = null)
    {
        if ($language === null) {
            $language = $this->defaultLanguage;
        }

        $results = array();
        foreach ($rows as $row) {
            $node = $this->nodeManager->find($row['id']);

            if (!$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') && !$this->authorizationChecker->isGranted('VIEW', $node)) {
                continue;
            }

            $element = $this->elementService->findElement($node->getContentId());
            $elementVersion = $this->elementService->findElementVersion($element, $element->getLatestVersion());
            $siteroot = $this->siterootManager->find($node->getTree()->getSiterootId());

            $handlerData = array(
                'handler' => 'element',
                'parameters' => array(
                    'id' => $node->getId(),
                    'siteroot_id' => $node->getTree()->getSiterootId(),
                    'title' => $siteroot->getTitle($language),
                    'start_tid_path' => '/' . implode('/', $node->getTree()->getIdPath($node)),
                )
            );

            try {
                $createUser = $elementVersion->getCreateUserId();
            } catch (\Exception $e) {
                $createUser = 'Unknown';
            }

            $icon = $this->iconResolver->resolveNode($node, $language);

            $results[] = new SearchResult(
                $node->getId(),
                $siteroot->getTitle($language) . ' :: ' . $elementVersion->getBackendTitle($language) . ' (' . $language . ', ' . $node->getId() . ')',
                $createUser,
                $elementVersion->getCreatedAt(),
                $icon,
                $title,
                $handlerData
            );
        }

        return $results;
    }
}
