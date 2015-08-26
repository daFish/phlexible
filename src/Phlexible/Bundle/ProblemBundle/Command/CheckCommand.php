<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ProblemBundle\Command;

use Phlexible\Bundle\ProblemBundle\Problem\ProblemCollection;
use Phlexible\Bundle\ProblemBundle\ProblemMessage;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Check command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CheckCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('problem:check')
            ->setDescription('Run cached problem checks.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $problemCheckers = $this->getContainer()->get('phlexible_problem.problem_checkers');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $problemsRepository = $em->getRepository('PhlexibleProblemBundle:Problem');

        $existing = new ProblemCollection($problemsRepository->findAll());
        $countExisting = count($existing);

        $known = array();
        $added = array();
        $removed = array();

        foreach ($problemCheckers as $problemChecker) {
            $problems = $problemChecker->check();

            $output->writeln(get_class($problemChecker) . ' => ' . count($problems));

            foreach ($existing->diff($problems) as $problem) {
                $output->writeln("<error> + {$problem->getId()}</error>");
                $em->persist($problem);
                $added[] = $problem->getId();
            }

            foreach ($existing->intersect($problems) as $problem) {
                $output->writeln(" = {$problem->getId()}");
                $existing = $problems->diff($existing);
                $known[] = $problem->getId();
            }
        }

        foreach ($existing as $problem) {
            $output->writeln("<info> - {$problem->getId()}</info>");
            $em->remove($problem);
            $removed[] = $problem->getId();
        }

        $em->flush();

        $properties = $this->getContainer()->get('properties');
        $properties->set('problems', 'last_run', date('Y-m-d H:i:s'));

        $type = ProblemMessage::TYPE_INFO;
        $body = '';
        $countKnown = count($known);
        $countAdd = count($added);
        $countRemove = count($removed);
        $parts = array();
        if ($known) {
            $body .= 'Known: ' . PHP_EOL . ' = ' . implode(PHP_EOL . ' = ', $known) . PHP_EOL;
            $parts[] = "$countKnown existing";
            $type = ProblemMessage::TYPE_ERROR;
        }
        if ($added) {
            $body .= 'Added: ' . PHP_EOL . ' + ' . implode(PHP_EOL . ' + ', $added) . PHP_EOL;
            $parts[] = "$countAdd added";
            $type = ProblemMessage::TYPE_ERROR;
        }
        if ($removed) {
            $body .= 'Removed: ' . PHP_EOL . ' - ' . implode(PHP_EOL . ' - ', $removed) . PHP_EOL;
            $parts[] = "$countRemove removed";
        }
        if (count($parts)) {
            $subject = "Problem check result: " . implode(" and ", $parts). " problems";
        } else {
            $subject = "Problem check result: no problems";
        }

        $this->getContainer()->get('phlexible_message.message_poster')
            ->post(ProblemMessage::create($subject, $body, $type));

        return 0;
    }
}
