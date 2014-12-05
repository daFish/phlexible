<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\Model;

/**
 * Meta set interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface MetaSetInterface
{
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
     * @return int
     */
    public function getRevision();

    /**
     * @param int $revision
     *
     * @return $this
     */
    public function setRevision($revision);

    /**
     * @return MetaSetFieldInterface[]
     */
    public function getFields();

    /**
     * @param MetaSetFieldInterface $field
     *
     * @return $this
     */
    public function addField(MetaSetFieldInterface $field);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasField($name);

    /**
     * @param string $name
     *
     * @return MetaSetField
     */
    public function getField($name);

    /**
     * @param int $id
     *
     * @return MetaSetFieldInterface
     */
    public function getFieldById($id);

    /**
     * @param MetaSetFieldInterface $field
     *
     * @return $this
     */
    public function removeField(MetaSetFieldInterface $field);

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
    public function getCreatedAt();

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);

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

    /**
     * @return \DateTime
     */
    public function getModifiedAt();

    /**
     * @param \DateTime $modifiedAt
     *
     * @return $this
     */
    public function setModifiedAt($modifiedAt);
}
