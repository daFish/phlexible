<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Tree;

use Doctrine\DBAL\Connection;
use Phlexible\Bundle\ElementBundle\Model\ElementHistoryManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Database tree
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DatabaseTreeFactory implements TreeFactoryInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var ElementHistoryManagerInterface
     */
    private $historyManager;

    /**
     * @param Connection                     $connection
     * @param EventDispatcherInterface       $dispatcher
     * @param ElementHistoryManagerInterface $historyManager
     */
    public function __construct(Connection $connection, EventDispatcherInterface $dispatcher, ElementHistoryManagerInterface $historyManager)
    {
        $this->connection = $connection;
        $this->dispatcher = $dispatcher;
        $this->historyManager = $historyManager;
    }

    /**
     * {@inheritdoc}
     */
    public function factory($siterootId)
    {
        return new DatabaseTree($siterootId, $this->connection, $this->dispatcher, $this->historyManager);
    }
}
