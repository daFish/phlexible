<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Message\Elastica;

use Elastica\Client;
use Elastica\Document;
use Elastica\Facet;
use Elastica\Filter;
use Elastica\Query;
use Elastica\ResultSet;
use Elastica\Type;
use Phlexible\Component\Message\Domain\Message;
use Phlexible\Component\Message\Model\MessageManagerInterface;
use Webmozart\Expression\Expression;

/**
 * Elastica message manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MessageManager implements MessageManagerInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $index;

    /**
     * @var string
     */
    private $type;

    /**
     * @param Client $client
     * @param string $index
     * @param string $type
     */
    public function __construct(Client $client, $index, $type = 'message')
    {
        $this->client = $client;
        $this->index = $index;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getFacets()
    {
        $query = new Query();
        $query->setSize(0);

        $typeFacet = new Facet\Terms('types');
        $typeFacet->setField('type');
        $query->addFacet($typeFacet);

        $channelFacet = new Facet\Terms('channels');
        $channelFacet->setField('channel');
        $query->addFacet($channelFacet);

        $roleFacet = new Facet\Terms('roles');
        $roleFacet->setField('role');
        $query->addFacet($roleFacet);

        $resultSet = $this->getType()->search($query);
        $facets = $resultSet->getFacets();
        $filterSets = array(
            'types'      => array_column($facets['types']['terms'], 'term'),
            'channels'   => array_column($facets['channels']['terms'], 'term'),
            'roles'      => array_column($facets['roles']['terms'], 'term'),
        );

        return $filterSets;
    }

    /**
     * Return facets
     *
     * @param Expression $expression
     *
     * @return array
     */
    public function getFacetsByExpression(Expression $expression)
    {
        $query = new Query();
        $query->setSize(0);

        $filter = $this->createFilterFromExpression($expression);

        $typeFacet = new Facet\Terms('types');
        $typeFacet->setField('type');
        if ($filter->getFilters()) {
            $typeFacet->setFilter($filter);
        }
        $query->addFacet($typeFacet);

        $channelFacet = new Facet\Terms('channels');
        $channelFacet->setField('channel');
        if ($filter->getFilters()) {
            $channelFacet->setFilter($filter);
        }
        $query->addFacet($channelFacet);

        $roleFacet = new Facet\Terms('roles');
        $roleFacet->setField('role');
        if ($filter->getFilters()) {
            $roleFacet->setFilter($filter);
        }
        $query->addFacet($roleFacet);

        $resultSet = $this->getType()->search($query);
        $facets = $resultSet->getFacets();

        $filterSets = array(
            'types'      => array_column($facets['types']['terms'], 'term'),
            'channels'   => array_column($facets['channels']['terms'], 'term'),
            'roles'      => array_column($facets['roles']['terms'], 'term'),
        );

        return $filterSets;
    }

    /**
     * @return Type
     */
    private function getType()
    {
        return $this->client->getIndex($this->index)->getType($this->type);
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        $document = $this->getType()->getDocument($id);

        if (!$document) {
            return null;
        }

        $message = $this->mapDocument($document);

        return $message;
    }

    /**
     * {@inheritdoc}
     */
    public function findByHandler($handler)
    {
        $handlerFilter = new Filter\Term();
        $handlerFilter->setTerm('handler', $handler);

        $query = new Query();
        $query->setPostFilter($handlerFilter);

        $documents = $this->getType()->search($query);
        $mesages = $this->mapDocuments($documents);

        return $mesages;
    }

    /**
     * {@inheritdoc}
     */
    public function findByExpression(Expression $expression, $orderBy = null, $limit = null, $offset = null)
    {
        $query = new Query();
        $this->applyExpressionToQuery($expression, $query);


        if ($limit !== null && $offset !== null) {
            $query
                ->setSize($limit)
                ->setFrom($offset);
        }

        if ($orderBy !== null) {
            $sort = array();
            foreach ($orderBy as $field => $dir) {
                $sort[] = array($field => strtolower($dir));
            }
            $query->setSort($sort);
        }

        $documents = $this->getType()->search($query);
        $mesages = $this->mapDocuments($documents);

        return $mesages;
    }

    /**
     * {@inheritdoc}
     */
    public function countByExpression(Expression $expression)
    {
        $query = new Query();
        $this->applyExpressionToQuery($expression, $query);

        return $this->getType()->count($query);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByExpression(Expression $expression, $orderBy = null, $limit = null, $offset = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria = array(), $orderBy = null, $limit = null, $offset = 0)
    {
        $query = new Query();

        if (count($criteria)) {
            $andFilter = new Filter\BoolAnd();
            foreach ($criteria as $key => $value) {
                $andFilter->addFilter(new Filter\Term(array($key => $value)));
            }
            $query->setPostFilter($andFilter);
        }

        if ($limit !== null && $offset !== null) {
            $query
                ->setSize($limit)
                ->setFrom($offset);
        }

        if ($orderBy !== null) {
            $sort = array();
            foreach ($orderBy as $field => $dir) {
                $sort[] = array($field => strtolower($dir));
            }
            $query->setSort($sort);
        }

        $documents = $this->getType()->search($query);
        $mesages = $this->mapDocuments($documents);

        return $mesages;
    }

    /**
     * Find message
     *
     * @param array $criteria
     * @param null  $orderBy
     *
     * @return Message[]
     */
    public function findOneBy(array $criteria, $orderBy = null)
    {
        $result = $this->findBy($criteria, $orderBy, 1);

        if (!count($result)) {
            return null;
        }

        return current($result);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeNames()
    {
        return array(
            0 => 'info',
            1 => 'error',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function updateMessage(Message $message)
    {
        $document = new Document(
            $message->getId(),
            array(
                'id'        => $message->getId(),
                'subject'   => $message->getSubject(),
                'body'      => $message->getBody(),
                'type'      => $message->getType(),
                'channel'   => $message->getChannel(),
                'role'      => $message->getRole(),
                'user'      => $message->getUser(),
                'createdAt' => $message->getCreatedAt()->format('U'),
            )
        );

        $this->getType()->addDocument($document);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMessage(Message $message)
    {
        $document = $this->find($message->getId());
        $this->getType()->deleteDocument($document);
    }

    /**
     * @param ResultSet $resultSet
     *
     * @return Message[]
     */
    private function mapDocuments(ResultSet $resultSet)
    {
        $mesages = array();

        foreach ($resultSet->getResults() as $result) {
            $mesages[] = $this->mapDocument($result->getSource());
        }

        return $mesages;
    }

    /**
     * @param array $row
     *
     * @return Message
     */
    private function mapDocument(array $row)
    {
        $message = Message::create(
            $row['subject'],
            $row['body'],
            $row['type'],
            $row['channel'],
            $row['role'],
            $row['user'],
            \DateTime::createFromFormat('U', $row['createdAt'])
        );

        return $message;
    }

    /**
     * Apply criteria to query
     *
     * @param Expression $expression
     * @param Query      $query
     * @param string     $prefix
     */
    private function applyExpressionToQuery(Expression $expression, Query $query, $prefix = '')
    {
        if (!count($expression)) {
            return;
        }

        $query->setPostFilter($this->createFilterFromExpression($expression));
    }

    private function createFilterFromExpression(Expression $expression)
    {
        $andFilter = new Filter\BoolAnd();

        $this->loopExpression($expression, $andFilter);

        return $andFilter;
    }

    private function loopExpression(Expression $expression, Filter\BoolAnd $andFilter)
    {
        foreach ($expression as $criterium) {
            if ($criterium instanceof Expression) {
                $this->loopExpression($criterium, $andFilter);
                continue;
            }

            $key = $criterium->getType();
            $type = $criterium->getType();
            $value = $criterium->getValue();

            if (is_string($value) && !strlen($value)) {
                continue;
            }

            switch ($type) {
                case 'like':
                    $andFilter->addFilter(new Filter\Query(new Query\Wildcard($key, '*' . $value . '*')));
                    break;

                case 'notLike':
                    $andFilter->addFilter(
                        new Filter\BoolNot(new Filter\Query(new Query\Wildcard($key, '*' . $value . '*')))
                    );
                    break;

                case 'eq':
                    $andFilter->addFilter(new Filter\Term(array($key => $value)));
                    break;

                case 'neq':
                    $orFilter = new Filter\BoolOr();
                    foreach (explode(',', $value) as $type) {
                        $orFilter->addFilter(new Filter\Term(array($key => $type)));
                    }
                    $andFilter->addFilter($orFilter);
                    break;

                case 'in':
                    $orFilter = new Filter\BoolOr();
                    foreach (explode(',', $value) as $channel) {
                        $orFilter->addFilter(new Filter\Term(array($key => $channel)));
                    }
                    $andFilter->addFilter($orFilter);
                    break;

                case 'greaterThan':
                    $andFilter->addFilter(new Filter\Range($key, array('gt' => $value)));
                    break;

                case 'greaterThanEquals':
                    $andFilter->addFilter(new Filter\Range($key, array('gte' => $value)));
                    break;

                case 'lessThan':
                    $andFilter->addFilter(new Filter\Range($key, array('lt' => $value)));
                    break;

                case 'lessThanEquals':
                    $andFilter->addFilter(new Filter\Range($key, array('lte' => $value)));
                    break;
            }
        }
    }
}
