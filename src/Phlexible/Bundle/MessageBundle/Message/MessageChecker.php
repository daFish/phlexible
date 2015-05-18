<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Message;

use Phlexible\Bundle\MessageBundle\Entity\Filter;
use Phlexible\Bundle\MessageBundle\Entity\Message;
use Phlexible\Bundle\MessageBundle\Exception\InvalidArgumentException;
use Webmozart\Expression\Expression;

/**
 * Message check
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MessageChecker
{
    /**
     * Check if message satisfies the given filter
     *
     * @param Filter  $filter
     * @param Message $message
     *
     * @return bool
     */
    public function checkByFilter(Filter $filter, Message $message)
    {
        return $this->check($filter->getExpression(), $message);
    }

    /**
     * Check if message satisfies the given expression
     *
     * @param Expression $expression
     * @param Message    $message
     *
     * @throws InvalidArgumentException
     * @return bool
     */
    public function check(Expression $expression, Message $message)
    {
        return $expression->evaluate(array(
            'subject'   => $message->getSubject(),
            'body'      => $message->getBody(),
            'type'      => $message->getType(),
            'channel'   => $message->getChannel(),
            'role'      => $message->getRole(),
            'user'      => $message->getUser(),
            'createdAt' => $message->getCreatedAt(),
        ));
    }
}
