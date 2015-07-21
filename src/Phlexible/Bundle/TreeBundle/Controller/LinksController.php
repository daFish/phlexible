<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Links controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/tree/links")
 * @Security("is_granted('ROLE_ELEMENTS')")
 */
class LinksController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="tree_links")
     */
    public function listAction(Request $request)
    {
        $nodeId = $request->get('nodeId');
        $language = $request->get('language');
        $version = $request->get('version');
        $incoming = $request->get('incoming', false);
        $limit = $request->get('limit', 25);
        $start = $request->get('start', 0);

        $nodeManager = $this->get('phlexible_tree.node_manager');
        $linkRepository = $this->getDoctrine()->getManager()->getRepository('PhlexibleTreeBundle:NodeLink');

        $node = $nodeManager->find($nodeId);

        $result = array();

        $queryBuilder = $linkRepository->createQueryBuilder('l');
        $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('l.nodeId', $node->getId()),
                    $queryBuilder->expr()->eq('l.version', $version),
                    $queryBuilder->expr()->eq('l.language', $queryBuilder->expr()->literal($language))
                )
            )
            ->setFirstResult($start)
            ->setMaxResults($limit);

        $countQueryBuilder = $linkRepository->createQueryBuilder('l')->select('COUNT(l.id)');
        $countQueryBuilder
            ->andWhere(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('l.nodeId', $node->getId()),
                    $queryBuilder->expr()->eq('l.version', $version),
                    $queryBuilder->expr()->eq('l.language', $queryBuilder->expr()->literal($language))
                )
            );

        if ($incoming) {
            $queryBuilder->orWhere(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('l.type', $queryBuilder->expr()->literal('node')),
                    $queryBuilder->expr()->eq('l.version', $version),
                    $queryBuilder->expr()->eq('l.language', $queryBuilder->expr()->literal($language)),
                    $queryBuilder->expr()->eq('l.target', $node->getId())
                )
            );
            $countQueryBuilder->orWhere(
                $queryBuilder->expr()->andX(
                    $countQueryBuilder->expr()->eq('l.type', $countQueryBuilder->expr()->literal('node')),
                    $countQueryBuilder->expr()->eq('l.version', $version),
                    $countQueryBuilder->expr()->eq('l.language', $countQueryBuilder->expr()->literal($language)),
                    $countQueryBuilder->expr()->eq('l.target', $node->getId())
                )
            );
        }

        $links = $queryBuilder->getQuery()->getResult();
        $total = $countQueryBuilder->getQuery()->getSingleScalarResult();


        foreach ($links as $link) {
            $result[] = array(
                'id'       => $link->getId(),
                'type'     => $link->getType(),
                'language' => $link->getLanguage(),
                'version'  => $link->getVersion(),
                'field'    => $link->getField(),
                'target'   => $link->getTarget(),
                'iconCls'  => 'p-element-component-icon',
                'link'     => array(),
            );
        }

        return new JsonResponse(array('links' => $result, 'total' => $total));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/search", name="elements_links_search")
     */
    public function searchAction(Request $request)
    {
        // TODO: switch to master language of element
        $defaultLanguage = $this->container->getParameter('phlexible_cms.languages.default');

        $language = $request->get('language', $defaultLanguage);
        $query = $request->get('query');
        $siterootId = $request->get('siteroot_id');
        $allowTid = $request->get('allow_tid');
        $allowIntrasiteroot = $request->get('allow_intrasiteroot');
        $elementTypeIds = $request->get('element_type_ids', '');

        $conn = $this->get('doctrine.dbal.default_connection');

        $qb = $conn->createQueryBuilder();

        if ($elementTypeIds) {
            $elementTypeIds = explode(',', $elementTypeIds);
            foreach ($elementTypeIds as $key => $elementTypeId) {
                $elementTypeIds[$key] = $qb->expr()->literal($elementTypeId);
            }
            $elementTypeIds = implode(',', $elementTypeIds);
        }

        $or = null;
        if (!$allowTid || !$allowIntrasiteroot) {
            if ($allowTid) {
                $where[] = $qb->expr()->eq('et.siteroot_id', $qb->expr()->literal($siterootId));
            }

            if ($allowIntrasiteroot) {
                $where[] = $qb->expr()->neq('et.siteroot_id', $qb->expr()->literal($siterootId));
            }

            $or = $qb->expr()->orX($where);
        }

        $qb
            ->select('t.id', 't.type_id AS eid', 't.siteroot_id', 'evmf.backend AS title')
            ->from('tree', 't')
            ->join('t', 'element', 'e', 't.type_id = e.eid')
            ->join('e', 'element_version', 'ev', 'e.eid = ev.eid AND e.latest_version = ev.version')
            ->join('ev', 'element_version_mapped_field', 'evmf', 'evmf.element_version_id = ev.id AND evmf.language = ' . $qb->expr()->literal($language))
            ->where($qb->expr()->eq('t.id', $qb->expr()->literal($query)))
            ->orderBy('title', 'ASC');

        if ($or) {
            $qb->andWhere($or);
        }

        if ($elementTypeIds) {
            $qb->join('e', 'element_version', 'ev', 'e.eid = ev.eid AND ev.element_type_id IN (' . $elementTypeIds . ')');
        }

        $results1 = $conn->fetchAll($qb->getSQL());

        $qb = $conn->createQueryBuilder();
        $qb
            ->select('t.id', 't.type_id AS eid', 't.siteroot_id', 'evmf.backend AS title')
            ->from('tree', 't')
            ->join('t', 'element', 'e', 't.type_id = e.eid')
            ->join('e', 'element_version', 'ev', 'e.eid = ev.eid AND e.latest_version = ev.version')
            ->join('ev', 'element_version_mapped_field', 'evmf', 'evmf.element_version_id = ev.id AND evmf.language = ' . $qb->expr()->literal($language))
            ->where($qb->expr()->like('evmf.backend', $qb->expr()->literal("%$query%")))
            ->orderBy('title', 'ASC');

        if ($or) {
            $qb->andWhere($or);
        }

        if ($elementTypeIds) {
            $qb->join('e', 'element_version', 'ev', 'e.eid = ev.eid AND ev.element_type_id IN (' . $elementTypeIds . ')');
        }

        $results2 = $conn->fetchAll($qb->getSQL());

        $results = array_merge($results1, $results2);

        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $data = array();
        foreach ($results as $row) {
            $siteroot = $siterootManager->find($row['siteroot_id']);
            $data[] = array(
                'id'    => $row['id'],
                'type'  => ($siterootId === $row['siteroot_id'] ? 'internal' : 'intrasiteroot'),
                'tid'   => $row['id'],
                'eid'   => $row['eid'],
                'title' => $siteroot->getTitle($language)
                    . ' :: ' . $row['title'] . ' [' . $row['id'] . ']',
            );
        }

        return new JsonResponse(array('results' => $data));
    }
}
