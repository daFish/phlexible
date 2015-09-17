<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaManager\Volume;

use Phlexible\Component\Volume\VolumeInterface;

/**
 * Extended volume interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ExtendedVolumeInterface extends VolumeInterface
{
    /**
     * @param ExtendedFolderInterface $folder
     * @param array                   $metasets
     * @param string                  $user
     *
     * @return ExtendedFolderInterface
     */
    public function setFolderMetasets(ExtendedFolderInterface $folder, array $metasets, $user);

    /**
     * @param ExtendedFileInterface $file
     * @param array                 $metasets
     * @param string                $user
     *
     * @return ExtendedFileInterface
     */
    public function setFileMetasets(ExtendedFileInterface $file, array $metasets, $user);

    /**
     * @param ExtendedFileInterface $file
     * @param string                $mediaType
     * @param string                $userId
     *
     * @return ExtendedFileInterface
     */
    public function setFileMediaType(ExtendedFileInterface $file, $mediaType, $userId);

    /**
     * @param ExtendedFileInterface $file
     * @param string                $mimeType
     * @param string                $userId
     *
     * @return ExtendedFileInterface
     */
    public function setFileMimeType(ExtendedFileInterface $file, $mimeType, $userId);
}
