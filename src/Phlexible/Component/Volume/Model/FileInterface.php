<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Volume\Model;

use Phlexible\Component\Volume\VolumeInterface;

/**
 * File interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface FileInterface
{
    /**
     * @return VolumeInterface
     */
    public function getVolume();

    /**
     * @param VolumeInterface $volume
     *
     * @return $this
     */
    public function setVolume(VolumeInterface $volume);

    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getVersion();

    /**
     * @param int $version
     *
     * @return $this
     */
    public function setVersion($version);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * @return FolderInterface
     */
    public function getFolder();

    /**
     * @param FolderInterface $folder
     *
     * @return $this
     */
    public function setFolder(FolderInterface $folder);

    /**
     * @return string
     */
    public function getFolderId();

    /**
     * @return string
     */
    public function getMimeType();

    /**
     * @param string $mimeType
     *
     * @return $this
     */
    public function setMimeType($mimeType);

    /**
     * @return int
     */
    public function getSize();

    /**
     * @param int $size
     *
     * @return $this
     */
    public function setSize($size);

    /**
     * @return string
     */
    public function getHash();

    /**
     * @param string $hash
     *
     * @return $this
     */
    public function setHash($hash);

    /**
     * @return bool
     */
    public function isHidden();

    /**
     * @param bool $hidden
     *
     * @return $this
     */
    public function setHidden($hidden = true);

    /**
     * @return array
     */
    public function getAttributes();

    /**
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes);

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getAttribute($key, $default = null);

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function setAttribute($key, $value);

    /**
     * @param string $key
     *
     * @return $this
     */
    public function removeAttribute($key);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * @return string
     */
    public function getCreateUser();

    /**
     * @param string $createUser
     *
     * @return $this
     */
    public function setCreateUser($createUser);

    /**
     * @return \DateTime
     */
    public function getModifiedAt();

    /**
     * @param \DateTime $modifiedAt
     *
     * @return $this
     */
    public function setModifiedAt(\DateTime $modifiedAt);

    /**
     * @return string
     */
    public function getModifyUser();

    /**
     * @param string $modifyUser
     *
     * @return $this
     */
    public function setModifyUser($modifyUser);
}
