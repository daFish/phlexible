<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Command;

use Phlexible\Component\Expression\Serializer\ArrayExpressionSerializer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * List command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ListCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('message:list')
            ->setDescription('Show latest messages')
            ->setDefinition(
                array(
                    new InputOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Show latest <limit> messages.', 20),
                    new InputOption('filter', 'f', InputOption::VALUE_REQUIRED, 'Filter name'),
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $limit = $input->getOption('limit');
        $filterName = $input->getOption('filter');
        $showBody = false;

        $messageManager = $this->getContainer()->get('phlexible_message.message_manager');
        $filterManager = $this->getContainer()->get('phlexible_message.filter_manager');
        $types = $messageManager->getTypeNames();

        $filter = $filterManager->findOneBy(array('title' => $filterName));

        if (!$filter) {
            $output->writeln("Filter $filterName not found.");

            return 1;
        }
        $t = new ArrayExpressionSerializer();
        $s = $this->getContainer()->get('serializer');
        dump($t->serialize($filter->getExpression()));
        dump($s->serialize($filter, 'json'));
        die;

        $messages = $messageManager->findByExpression($filter->getExpression(), array('createdAt' => 'DESC'), $limit);
        $messages = array_reverse($messages);
        foreach ($messages as $message) {
            $output->writeln(
                sprintf(
                    "[%s] %s: %s [%s, %s]",
                    $message->getCreatedAt()->format('Y-m-d H:i:s'),
                    $types[$message->getType()],
                    $message->getSubject(),
                    $message->getChannel() ? : '-',
                    $message->getRole() ? : '-'
                )
            );
            if ($showBody) {
                $output->writeln(' > ' . $message->getBody());
            }
        }

        return 0;
    }

}