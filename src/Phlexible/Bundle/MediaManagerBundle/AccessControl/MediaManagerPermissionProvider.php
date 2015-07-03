<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle\AccessControl;

use Phlexible\Component\AccessControl\Permission\Permission;
use Phlexible\Component\AccessControl\Permission\PermissionCollection;
use Phlexible\Component\AccessControl\Permission\PermissionProviderInterface;

/**
 * Media manager permission provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaManagerPermissionProvider implements PermissionProviderInterface
{
    /**
     * @var string
     */
    private $contentClass;

    /**
     * @param string $contentClass
     */
    public function __construct($contentClass = 'Phlexible\Bundle\MediaManagerBundle\Entity\Folder')
    {
        $this->contentClass = $contentClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissions()
    {
        return new PermissionCollection(array(
            new Permission($this->contentClass, 'FOLDER_READ', 1, 'p-mediamanager-folder_view-icon'),
            new Permission($this->contentClass, 'FOLDER_CREATE', 2, 'p-mediamanager-folder_add-icon'),
            new Permission($this->contentClass, 'FOLDER_MODIFY', 4, 'p-mediamanager-folder_edit-icon'),
            new Permission($this->contentClass, 'FOLDER_DELETE', 8, 'p-mediamanager-folder_delete-icon'),
            new Permission($this->contentClass, 'FOLDER_RIGHTS', 16, 'p-mediamanager-folder_rights-icon'),
            new Permission($this->contentClass, 'FILE_READ', 32, 'p-mediamanager-file_view-icon'),
            new Permission($this->contentClass, 'FILE_CREATE', 64, 'p-mediamanager-file_add-icon'),
            new Permission($this->contentClass, 'FILE_MODIFY', 128, 'p-mediamanager-file_edit-icon'),
            new Permission($this->contentClass, 'FILE_DELETE', 256, 'p-mediamanager-file_delete-icon'),
            new Permission($this->contentClass, 'FILE_DOWNLOAD', 512, 'p-mediamanager-file_download-icon'),
        ));
    }
}
