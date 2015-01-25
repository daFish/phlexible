<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Criteria;

use Phlexible\Bundle\MessageBundle\Exception\InvalidArgumentException;

/**
 * Message criteria
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Criteria implements CriteriumInterface, \IteratorAggregate, \Countable
{
    const MODE_OR = 'or';
    const MODE_AND = 'and';

    const CRITERIUM_SUBJECT_LIKE     = 'subjectLike';
    const CRITERIUM_SUBJECT_NOT_LIKE = 'subjectNotLike';
    const CRITERIUM_BODY_LIKE        = 'bodyLike';
    const CRITERIUM_BODY_NOT_LIKE    = 'bodyNotLike';
    const CRITERIUM_USER_LIKE        = 'userLike';
    const CRITERIUM_USER_NOT_LIKE    = 'userNotLike';
    const CRITERIUM_PRIORITY_IS      = 'priorityIs';
    const CRITERIUM_PRIORITY_IN      = 'priorityIn';
    const CRITERIUM_PRIORITY_MIN     = 'priorityMin';
    const CRITERIUM_TYPE_IS          = 'typeIs';
    const CRITERIUM_TYPE_IN          = 'typeIn';
    const CRITERIUM_CHANNEL_IS       = 'channelIs';
    const CRITERIUM_CHANNEL_LIKE     = 'channelLike';
    const CRITERIUM_CHANNEL_IN       = 'channelIn';
    const CRITERIUM_ROLE_IS          = 'roleIs';
    const CRITERIUM_ROLE_IN          = 'roleIn';
    const CRITERIUM_MIN_AGE          = 'minAge';
    const CRITERIUM_MAX_AGE          = 'maxAge';
    const CRITERIUM_START_DATE       = 'startDate';
    const CRITERIUM_END_DATE         = 'endDate';
    const CRITERIUM_DATE_IS          = 'dateIs';

    /**
     * @var CriteriumInterface[]
     */
    private $criteria = [];

    /**
     * @var string
     */
    private $mode = self::MODE_AND;

    /**
     * @param array  $criteria
     * @param string $mode
     */
    public function __construct(array $criteria = [], $mode = self::MODE_AND)
    {
        $this->criteria = $criteria;
        $this->mode = $mode;
    }

    /**
     * @param string $mode
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setMode($mode)
    {
        if (!in_array($mode, [self::MODE_AND, self::MODE_OR])) {
            throw new InvalidArgumentException("Invalid mode $mode");
        }
        $this->mode = $mode;

        return $this;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $data = [];
        foreach ($this->criteria as $criterium) {
            $data[] = $criterium->toArray();
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'collection';
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->criteria;
    }

    /**
     * @param CriteriumInterface $criterium
     *
     * @return $this
     */
    public function add(CriteriumInterface $criterium)
    {
        $this->criteria[] = $criterium;

        return $this;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->criteria;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->criteria);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->criteria);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function subjectLike($value)
    {
        $this->add(new Criterium(self::CRITERIUM_SUBJECT_LIKE, $value));

        return $this;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function subjectNotLike($value)
    {
        $this->add(new Criterium(self::CRITERIUM_SUBJECT_NOT_LIKE, $value));

        return $this;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function bodyLike($value)
    {
        $this->criteria[] = new Criterium(self::CRITERIUM_BODY_LIKE, $value);

        return $this;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function bodyNotLike($value)
    {
        $this->add(new Criterium(self::CRITERIUM_BODY_NOT_LIKE, $value));

        return $this;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function userLike($value)
    {
        $this->criteria[] = new Criterium(self::CRITERIUM_USER_LIKE, $value);

        return $this;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function userNotLike($value)
    {
        $this->add(new Criterium(self::CRITERIUM_USER_NOT_LIKE, $value));

        return $this;
    }

    /**
     * @param int $value
     *
     * @return $this
     */
    public function priorityIs($value)
    {
        $this->add(new Criterium(self::CRITERIUM_PRIORITY_IS, (int) $value));

        return $this;
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    public function priorityIn(array $values)
    {
        foreach ($values as $index => $value) {
            $values[$index] = (int) $value;
        }
        $this->add(new Criterium(self::CRITERIUM_PRIORITY_IN, $values));

        return $this;
    }

    /**
     * @param int $value
     *
     * @return $this
     */
    public function priorityMin($value)
    {
        $this->add(new Criterium(self::CRITERIUM_PRIORITY_MIN, (int) $value));

        return $this;
    }

    /**
     * @param int $value
     *
     * @return $this
     */
    public function typeIs($value)
    {
        $this->add(new Criterium(self::CRITERIUM_TYPE_IS, (int) $value));

        return $this;
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    public function typeIn(array $values)
    {
        foreach ($values as $index => $value) {
            $values[$index] = (int) $value;
        }
        $this->add(new Criterium(self::CRITERIUM_TYPE_IN, $values));

        return $this;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function channelIs($value)
    {
        $this->add(new Criterium(self::CRITERIUM_CHANNEL_IS, $value));

        return $this;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function channelLike($value)
    {
        $this->add(new Criterium(self::CRITERIUM_CHANNEL_LIKE, $value));

        return $this;
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    public function channelIn(array $values)
    {
        $this->add(new Criterium(self::CRITERIUM_CHANNEL_IN, $values));

        return $this;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function roleIs($value)
    {
        $this->add(new Criterium(self::CRITERIUM_ROLE_IS, $value));

        return $this;
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    public function roleIn(array $values)
    {
        $this->add(new Criterium(self::CRITERIUM_ROLE_IN, $values));

        return $this;
    }

    /**
     * @param int $value
     *
     * @return $this
     */
    public function minAge($value)
    {
        $this->add(new Criterium(self::CRITERIUM_MIN_AGE, $value));

        return $this;
    }

    /**
     * @param int $value
     *
     * @return $this
     */
    public function maxAge($value)
    {
        $this->add(new Criterium(self::CRITERIUM_MAX_AGE, $value));

        return $this;
    }

    /**
     * @param \DateTime $value
     *
     * @return $this
     */
    public function dateFrom(\DateTime $value)
    {
        $this->add(new Criterium(self::CRITERIUM_START_DATE, $value));

        return $this;
    }

    /**
     * @param \DateTime $value
     *
     * @return $this
     */
    public function dateTo(\DateTime $value)
    {
        $this->add(new Criterium(self::CRITERIUM_END_DATE, $value));

        return $this;
    }

    /**
     * @param \DateTime $value
     *
     * @return $this
     */
    public function dateIs(\DateTime $value)
    {
        $this->add(new Criterium(self::CRITERIUM_DATE_IS, $value));

        return $this;
    }
}
