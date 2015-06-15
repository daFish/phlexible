<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\DataSourceBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\DataSourceBundle\Entity\DataSource;
use Phlexible\Bundle\DataSourceBundle\Form\Type\DataSourceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Data sources controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_DATA_SOURCES')")
 * @Rest\NamePrefix("phlexible_api_datasource_")
 */
class DataSourcesController extends FOSRestController
{
    /**
     * Return data sources
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a collection of DataSource",
     *   section="datasource",
     *   resource=true,
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */
    public function getDatasourcesAction()
    {
        $dataSourceManager = $this->get('phlexible_data_source.data_source_manager');
        $dataSources = $dataSourceManager->findBy([]);

        return array(
            'datasources' => $dataSources,
            'count'       => count($dataSources),
        );
    }

    /**
     * Return data source
     *
     * @param string $dataSourceId
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a DataSource",
     *   section="datasource",
     *   output="Phlexible\Bundle\DataSourceBundle\Entity\DataSource",
     *   statusCodes={
     *     200="Returned when successful",
     *     404="Returned when datasource was not found"
     *   }
     * )
     */
    public function getDatasourceAction($dataSourceId)
    {
        $dataSourceManager = $this->get('phlexible_data_source.data_source_manager');
        $dataSource = $dataSourceManager->find($dataSourceId);

        if (!$dataSource instanceof DataSource) {
            throw new NotFoundHttpException('Job not found');
        }

        return array(
            'datasource' => $dataSource
        );
    }

    /**
     * Create data source
     *
     * @param Request $request
     *
     * @return Response
     *
     * @ApiDoc(
     *   description="Create a DataSource",
     *   section="datasource",
     *   input="Phlexible\Bundle\DataSourceBundle\Form\Type\DataSourceType",
     *   statusCodes={
     *     201="Returned when datasource was created",
     *     204="Returned when datasource was updated",
     *     404="Returned when datasource was not found"
     *   }
     * )
     */
    public function postDatasourcesAction(Request $request)
    {
        return $this->processForm($request, new DataSource());
    }

    /**
     * Create data source value
     *
     * @param string $datasourceId
     *
     * @return Response
     */
    public function postDatasourceValuesAction($datasourceId)
    {
        $key = $request->get('key');
        $language = $request->getLocale();

        $dataSourceManager = $this->get('phlexible_data_source.data_source_manager');

        // load
        $source = $dataSourceManager->find($datasourceId);

        // add new key
        $source->addValueForLanguage($key, false);

        // save
        $dataSourceManager->updateDataSource($source);

        return $this->handleView($this->view(
            array(
                'success' => true,
            )
        ));
    }

    /**
     * @param Request    $request
     * @param DataSource $dataSource
     *
     * @return Rest\View|Response
     */
    private function processForm(Request $request, DataSource $dataSource)
    {
        $statusCode = !$dataSource->getId() ? 201 : 204;

        $form = $this->createForm(new DataSourceType(), $dataSource);
        $form->submit($request);

        if ($form->isValid()) {
            $dataSourceManager = $this->get('phlexible_data_source.data_source_manager');
            $dataSourceManager->updateTemplate($dataSource);

            $response = new Response();
            $response->setStatusCode($statusCode);

            // set the `Location` header only when creating new resources
            if (201 === $statusCode) {
                $response->headers->set('Location',
                    $this->generateUrl(
                        'phlexible_api_queue_get_job', array('datasourceId' => $dataSource->getId()),
                        true // absolute
                    )
                );
            }

            return $response;
        }

        return View::create($form, 400);
    }
}
