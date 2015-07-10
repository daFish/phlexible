<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Elastica;

use Elastica\Client;
use Elastica\Document;
use Elastica\Facet;
use Elastica\Filter;
use Elastica\Query;
use Elastica\ResultSet;
use Elastica\Type;
use Phlexible\Bundle\MessageBundle\Criteria\Criteria;
use Phlexible\Bundle\MessageBundle\Entity\Message;
use Phlexible\Bundle\MessageBundle\Model\MessageManagerInterface;

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

        $priorityFacet = new Facet\Terms('priorities');
        $priorityFacet->setField('priority');
        $query->addFacet($priorityFacet);

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
            'priorities' => array_column($facets['priorities']['terms'], 'term'),
            'types'      => array_column($facets['types']['terms'], 'term'),
            'channels'   => array_column($facets['channels']['terms'], 'term'),
            'roles'      => array_column($facets['roles']['terms'], 'term'),
        );

        return $filterSets;
    }

    /**
     * Return facets
     *
     * @param Criteria $criteria
     *
     * @return array
     */
    public function getFacetsByCriteria(Criteria $criteria)
    {

        $query = new Query();
        $query->setSize(0);

        $filter = $this->createFilterFromCriteria($criteria);

        $priorityFacet = new Facet\Terms('priorities');
        $priorityFacet->setField('priority');
        if ($filter->getFilters()) {
            $priorityFacet->setFilter($filter);
        }
        $query->addFacet($priorityFacet);

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
            'priorities' => array_column($facets['priorities']['terms'], 'term'),
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

        $query = new Filter\Query();
        $query->setPostFilter($handlerFilter);

        $documents = $this->getType()->search($query);
        $mesages = $this->mapDocuments($documents);

        return $mesages;
    }

    /**
     * {@inheritdoc}
     */
    public function findByCriteria(Criteria $criteria, $order = null, $limit = null, $offset = null)
    {
        $query = new Query();
        $this->applyCriteriaToQuery($criteria, $query);


        if ($limit !== null && $offset !== null) {
            $query
                ->setSize($limit)
                ->setFrom($offset);
        }

        if ($order !== null) {
            $sort = array();
            foreach ($order as $field => $dir) {
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
    public function countByCriteria(Criteria $criteria)
    {
        $query = new Query();
        $this->applyCriteriaToQuery($criteria, $query);

        return $this->getType()->count($query);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria = array(), $order = null, $limit = null, $offset = 0)
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

        if ($order !== null) {
            $sort = array();
            foreach ($order as $field => $dir) {
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
        // TODO: Implement findOneBy() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getPriorityNames()
    {
        return array(
            0 => 'low',
            1 => 'normal',
            2 => 'high',
            3 => 'urgent',
        );
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
                'priority'  => $message->getPriority(),
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
            $row['priority'],
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
     * @param Criteria $criteria
     * @param Query    $query
     * @param string   $prefix
     */
    private function applyCriteriaToQuery(Criteria $criteria, Query $query, $prefix = '')
    {
        if (!count($criteria)) {
            return;
        }

        $query->setPostFilter($this->createFilterFromCriteria($criteria));
    }

    private function createFilterFromCriteria(Criteria $criteria)
    {
        $andFilter = new Filter\BoolAnd();

        $this->loopCriteria($criteria, $andFilter);

        return $andFilter;
    }

    private function loopCriteria(Criteria $criteria, Filter\BoolAnd $andFilter)
    {
        foreach ($criteria as $criterium) {
            if ($criterium instanceof Criteria) {
                $this->loopCriteria($criterium, $andFilter);
                continue;
            }

            $type = $criterium->getType();
            $value = $criterium->getValue();

            if (is_string($value) && !strlen($value)) {
                continue;
            }

            switch ($type) {
                case Criteria::CRITERIUM_SUBJECT_LIKE:
                    $andFilter->addFilter(new Filter\Query(new Query\Wildcard('subject', '*' . $value . '*')));
                    break;

                case Criteria::CRITERIUM_SUBJECT_NOT_LIKE:
                    $andFilter->addFilter(
                        new Filter\BoolNot(new Filter\Query(new Query\Wildcard('subject', '*' . $value . '*')))
                    );
                    break;

                case Criteria::CRITERIUM_BODY_LIKE:
                    $andFilter->addFilter(new Filter\Query(new Query\Wildcard('body', '*' . $value . '*')));
                    break;

                case Criteria::CRITERIUM_BODY_NOT_LIKE:
                    $andFilter->addFilter(
                        new Filter\BoolNot(new Filter\Query(new Query\Wildcard('body', '*' . $value . '*')))
                    );
                    break;

                case Criteria::CRITERIUM_PRIORITY_IS:
                    $andFilter->addFilter(new Filter\Term(array('priority', $value)));
                    break;

                case Criteria::CRITERIUM_PRIORITY_MIN:
                    $andFilter->addFilter(new Filter\Range('priority', array('gte' => $value)));
                    break;

                case Criteria::CRITERIUM_PRIORITY_IN:
                    $orFilter = new Filter\BoolOr();
                    foreach (explode(',', $value) as $priority) {
                        $orFilter->addFilter(new Filter\Term(array('priority' => $priority)));
                    }
                    $andFilter->addFilter($orFilter);
                    break;

                case Criteria::CRITERIUM_TYPE_IS:
                    $andFilter->addFilter(new Filter\Term(array('type' => $value)));
                    break;

                case Criteria::CRITERIUM_TYPE_IN:
                    $orFilter = new Filter\BoolOr();
                    foreach (explode(',', $value) as $type) {
                        $orFilter->addFilter(new Filter\Term(array('type' => $type)));
                    }
                    $andFilter->addFilter($orFilter);
                    break;

                case Criteria::CRITERIUM_CHANNEL_IS:
                    $andFilter->addFilter(new Filter\Term(array('channel' => $value)));
                    break;

                case Criteria::CRITERIUM_CHANNEL_LIKE:
                    $andFilter->addFilter(new Filter\Query(new Query\Wildcard('channel', '*' . $value . '*')));
                    break;

                case Criteria::CRITERIUM_CHANNEL_IN:
                    $orFilter = new Filter\BoolOr();
                    foreach (explode(',', $value) as $channel) {
                        $orFilter->addFilter(new Filter\Term(array('channel' => $channel)));
                    }
                    $andFilter->addFilter($orFilter);
                    break;

                case Criteria::CRITERIUM_ROLE_IS:
                    $andFilter->addFilter(new Filter\Term(array('role' => $value)));
                    break;

                case Criteria::CRITERIUM_ROLE_IN:
                    $orFilter = new Filter\BoolOr();
                    foreach (explode(',', $value) as $role) {
                        $orFilter->addFilter(new Filter\Term(array('role' => $role)));
                    }
                    $andFilter->addFilter($orFilter);
                    break;

                case Criteria::CRITERIUM_MAX_AGE:
                    $andFilter->addFilter(new Filter\Range('createdAt', array('gt' => time() - ($value * 24 * 60 * 60))));
                    break;

                case Criteria::CRITERIUM_MIN_AGE:
                    $andFilter->addFilter(new Filter\Range('createdAt', array('lt' => time() - ($value * 24 * 60 * 60))));
                    break;

                case Criteria::CRITERIUM_START_DATE:
                    $andFilter->addFilter(new Filter\Range('createdAt', array('gt' => $value->format('U'))));
                    break;

                case Criteria::CRITERIUM_END_DATE:
                    $andFilter->addFilter(new Filter\Range('createdAt', array('lt' => $value->format('U'))));
                    break;

                case Criteria::CRITERIUM_DATE_IS:
                    $andFilter->addFilter(
                        new Filter\Range('createdAt', array(
                            'gte' => $value->format('U'),
                            'lt'  => $value->format('U'),
                        ))
                    );
                    break;
            }
        }
    }
}
