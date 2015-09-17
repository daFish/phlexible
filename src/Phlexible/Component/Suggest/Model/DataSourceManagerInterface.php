<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Suggest\Model;

use Phlexible\Component\Suggest\Domain\DataSource;

/**
 * Data source manager interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface DataSourceManagerInterface
{
    /**
     * @param string $id
     *
     * @return DataSource
     */
    public function find($id);

    /**
     * @param array      $criteria
     * @param null|array $orderBy
     * @param null|int   $limit
     * @param null|int   $offset
     *
     * @return array|DataSource[]
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null);

    /**
     * @param DataSource $dataSource
     */
    public function updateDataSource(DataSource $dataSource);

    /**
     * @param DataSource $dataSource
     */
    public function deleteDataSource(DataSource $dataSource);
}
