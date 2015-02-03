<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\Controller;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\NoRoute;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Prefix;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\DataSourceBundle\Entity\DataSource;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * Data sources controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_DATA_SOURCES')")
 * @Prefix("/datasource")
 * @NamePrefix("phlexible_datasource_")
 */
class DatasourcesController extends FOSRestController
{
    /**
     * Return data sources
     *
     * @return Response
     *
     * @ApiDoc
     */
    public function getDatasourcesAction()
    {
        $dataSourceManager = $this->get('phlexible_data_source.data_source_manager');

        $dataSources = $dataSourceManager->findBy([]);

        return $this->handleView($this->view(
            array(
                'datasources' => $dataSources,
                'count'       => count($dataSources),
            )
        ));
    }

    /**
     * Return data source
     *
     * @param string $dataSourceId
     *
     * @return Response
     *
     * @View(templateVar="dataSource")
     * @ApiDoc
     */
    public function getDatasourceAction($dataSourceId)
    {
        $dataSourceManager = $this->get('phlexible_data_source.data_source_manager');

        $dataSource = $dataSourceManager->find($dataSourceId);

        return $dataSource;
    }

    /**
     * Create data source
     *
     * @param DataSource $datasource
     *
     * @return Response
     *
     * @ParamConverter("datasource", converter="fos_rest.request_body")
     * @Post("/datasources")
     * @ApiDoc
     */
    public function postDatasourcesAction(DataSource $datasource)
    {
        $dataSourceManager = $this->get('phlexible_data_source.data_source_manager');

        $dataSourceManager->updateDataSource($datasource);

        return $this->handleView($this->view(
            array(
                'success' => true,
            )
        ));
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
        $source = $dataSourceManager->find($dataSourceId);

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
}
