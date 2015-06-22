<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\DashboardBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Info controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/dashboard/info")
 */
class InfoController extends Controller
{
    /**
     * Return info
     *
     * @param Request $request
     *
     * @return Response
     * @Route("", name="dashboard_info")
     */
    public function infoAction(Request $request)
    {
        $securityContext = $this->get('security.context');

        $projectTitle = $this->container->getParameter('phlexible_gui.project.title');
        $projectVersion = $this->container->getParameter('phlexible_gui.project.version');

        $lines = [
            ['Project:', $projectTitle . ' ' . $projectVersion]
        ];

        if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {
            $connection = $this->getDoctrine()->getConnection();
            /* @var $connection \Doctrine\DBAL\Connection */

            $env = $this->container->getParameter('kernel.environment');
            $debug = $this->container->getParameter('kernel.debug');

            $serverName = $request->server->get('SERVER_NAME');
            $remoteAddr = $request->server->get('REMOTE_ADDR');

            $dbHost = $connection->getHost();
            $db = $connection->getDatabase();
            $dbName = $connection->getDriver()->getName();

            $sessionId = $request->getSession()->getId();

            $username = $this->getUser()->getUsername();
            $roles = $this->getUser()->getRoles();

            $lines[] = ['Env:', $env . ($debug ? ' [DEBUG]' : '')];
            $lines[] = ['Host:', $serverName . ' [' . PHP_SAPI . ']'];
            $lines[] = ['Default Database:', $dbHost . ' / ' . $db . ' [' . $dbName . ']'];
            $lines[] = ['Session:', $sessionId . ' [' . $remoteAddr . ']'];
            $lines[] = ['User:', $username . ' [' . implode(', ', $roles) . ']'];
            $lines[] = ['UserAgent:', $request->server->get('HTTP_USER_AGENT')];
        }

        return new Response("<pre>{$this->tableize($lines)}</pre>");
    }

    /**
     * @param array $lines
     *
     * @return string
     */
    private function tableize(array $lines)
    {
        $l1 = 0;
        $l2 = 0;
        foreach ($lines as $line) {
            if (strlen($line[0]) > $l1) {
                $l1 = strlen($line[0]);
            }
            if (isset($line[1]) && strlen($line[1]) > $l2) {
                $l2 = strlen($line[1]);
            }
        }
        $table = '';
        foreach ($lines as $line) {
            $table .= str_pad($line[0], $l1 + 2);
            $table .= str_pad($line[1], $l2 + 2);
            if (isset($line[2])) {
                $table .= $line[2];
            }
            $table .= PHP_EOL;
        }

        return $table;
    }
}
