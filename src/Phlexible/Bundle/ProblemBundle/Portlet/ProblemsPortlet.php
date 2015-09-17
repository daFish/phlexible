<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ProblemBundle\Portlet;

use Phlexible\Bundle\ProblemBundle\Entity\Problem;
use Phlexible\Bundle\ProblemBundle\Problem\ProblemFetcher;

/**
 * Problems portlet.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @author Phillip Look <pl@brainbits.net>
 */
class ProblemsPortlet extends \Phlexible\Bundle\DashboardBundle\Domain\Portlet
{
    /**
     * @var ProblemFetcher
     */
    private $fetcher;

    /**
     * @param ProblemFetcher $fetcher
     */
    public function __construct(ProblemFetcher $fetcher)
    {
        $this->fetcher = $fetcher;
    }

    /**
     * Return Portlet data.
     *
     * @return array
     */
    public function getData()
    {
        $data = array();

        $problems = $this->fetcher->fetch();

        $allowedSeverities = array(
            Problem::SEVERITY_CRITICAL,
            Problem::SEVERITY_WARNING,
        );

        foreach ($problems as $problem) {
            if (!in_array($problem->getSeverity(), $allowedSeverities)) {
                continue;
            }

            $data[] = array(
                'id' => strlen($problem->getId()) ? $problem->getId() : md5(serialize($problem)),
                'severity' => $problem->getSeverity(),
                'msg' => $problem->getMessage(),
                'hint' => $problem->getHint(),
                'link' => $problem->getAttribute('link'),
            );
        }

        if (!count($data)) {
            $data = false;
        }

        return $data;
    }
}
