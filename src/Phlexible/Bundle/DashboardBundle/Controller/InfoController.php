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

        $lines = array($this->createProject());

        if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {
            $lines[] = $this->createEnv();
            $lines[] = $this->createHost($request);
            $lines[] = $this->createDatabase();
            $lines[] = $this->createSession($request);
            $lines[] = $this->createUser();
            $lines[] = $this->createUserAgent($request);
        }

        return new Response("<pre>{$this->tableize($lines)}</pre>");
    }

    /**
     * @return array
     */
    private function createProject()
    {
        $projectTitle = $this->container->getParameter('phlexible_gui.project.title');
        $projectVersion = $this->container->getParameter('phlexible_gui.project.version');

        return array('Project:', $projectTitle . ' ' . $projectVersion);
    }

    /**
     * @return array
     */
    private function createEnv()
    {
        $env = $this->container->getParameter('kernel.environment');
        $debug = $this->container->getParameter('kernel.debug');

        return array('Env:', $env . ($debug ? ' [DEBUG]' : ''));
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function createHost(Request $request)
    {
        $serverName = $request->server->get('SERVER_NAME');

        return array('Host:', $serverName . ' [' . PHP_SAPI . ']');
    }

    /**
     * @return array
     */
    private function createDatabase()
    {
        $connection = $this->getDoctrine()->getConnection();
        /* @var $connection \Doctrine\DBAL\Connection */

        $dbHost = $connection->getHost();
        $db = $connection->getDatabase();
        $dbName = $connection->getDriver()->getName();

        return array('Default Database:', $dbHost . ' / ' . $db . ' [' . $dbName . ']');
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function createSession(Request $request)
    {
        $sessionId = $request->getSession()->getId();
        $remoteAddress = $request->server->get('REMOTE_ADDR');

        return array('Session:', $sessionId . ' [' . $remoteAddress . ']');
    }

    /**
     * @return array
     */
    private function createUser()
    {
        $username = $this->getUser()->getUsername();
        $roles = $this->getUser()->getRoles();

        return array('User:', $username . ' [' . implode(', ', $roles) . ']');
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function createUserAgent(Request $request)
    {
        return array('UserAgent:', $request->server->get('HTTP_USER_AGENT'));
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
