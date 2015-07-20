<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Node mapped field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="node_mapped_field")
 */
class NodeMappedField
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="node_id", type="integer")
     */
    private $nodeId;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @var string
     * @ORM\Column(type="string", length=2, options={"fixed"=true})
     */
    private $language;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $backend;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $page;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $navigation;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $forward;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $custom1;

    /**
     * @var string
     * @ORM\Column(name="custom1_name", type="string", length=255, nullable=true)
     */
    private $custom1Name;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $custom2;

    /**
     * @var string
     * @ORM\Column(name="custom2_name", type="string", length=255, nullable=true)
     */
    private $custom2Name;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $custom3;

    /**
     * @var string
     * @ORM\Column(name="custom3_name", type="string", length=255, nullable=true)
     */
    private $custom3Name;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $custom4;

    /**
     * @var string
     * @ORM\Column(name="custom4_name", type="string", length=255, nullable=true)
     */
    private $custom4Name;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $custom5;

    /**
     * @var string
     * @ORM\Column(name="custom5_name", type="string", length=255, nullable=true)
     */
    private $custom5Name;

    /**
     * Return mapped field
     *
     * @param string $field
     * @param string $language
     * @param string $fallbackLanguage
     *
     * @return string
     */
    public function getMappedField($field, $language, $fallbackLanguage = null)
    {
        if ($this->mappedFields->containsKey($language)) {
            $mappedField = $this->mappedFields->get($language);
        } elseif ($this->mappedFields->containsKey($fallbackLanguage)) {
            $mappedField = $this->mappedFields->get($fallbackLanguage);
        } else {
            foreach ($this->mappedFields as $testMappedField) {
                if ($testMappedField->getLanguage() === $language) {
                    $mappedField = $testMappedField;
                    break;
                }
            }
            if (!isset($mappedField)) {
                foreach ($this->mappedFields as $testMappedField) {
                    if ($testMappedField->getLanguage() === $fallbackLanguage) {
                        $mappedField = $testMappedField;
                        break;
                    }
                }
            }
            if (!isset($mappedField)) {
                return null;
            }
        }

        if ($field === 'page') {
            if ($mappedField->getPage()) {
                return $mappedField->getPage();
            } else {
                $field = 'backend';
            }
        }

        if ($field === 'navigation') {
            if ($mappedField->getNavigation()) {
                return $mappedField->getNavigation();
            } else {
                $field = 'backend';
            }
        }

        if ($field === 'backend') {
            return $mappedField->getBackend();
        }

        if ($field === 'date' && $mappedField->getDate()) {
            return $mappedField->getDate();
        }

        if ($field === 'forward' && $mappedField->getForward()) {
            return json_decode($mappedField->getForward(), true);
        }

        return null;
    }

    /**
     * @param array $fields
     */
    public function __construct(array $fields = array())
    {
        $this->setMapping($fields);
    }

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function setMapping(array $fields = array())
    {
        $allowedFields = array('backend', 'page', 'navigation', 'date', 'forward', 'custom1', 'custom2', 'custom3', 'custom4', 'custom5');
        foreach ($fields as $field => $value) {
            if (!$value || !in_array($field, $allowedFields)) {
                continue;
            }
            if ($field === 'date' && is_string($value)) {
                $value = new \DateTime($value);
            }
            $this->$field = $value;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getNodeId()
    {
        return $this->nodeId;
    }

    /**
     * @param int $nodeId
     *
     * @return $this
     */
    public function setNodeId($nodeId)
    {
        $this->nodeId = $nodeId;

        return $this;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param int $version
     *
     * @return $this
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     *
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return string
     */
    public function getBackend()
    {
        return $this->backend;
    }

    /**
     * @param string $backend
     *
     * @return $this
     */
    public function setBackend($backend)
    {
        $this->backend = $backend;

        return $this;
    }

    /**
     * @return string
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param string $page
     *
     * @return $this
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return string
     */
    public function getNavigation()
    {
        return $this->navigation;
    }

    /**
     * @param string $navigation
     *
     * @return $this
     */
    public function setNavigation($navigation)
    {
        $this->navigation = $navigation;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     *
     * @return $this
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return string
     */
    public function getForward()
    {
        return $this->forward;
    }

    /**
     * @param string $forward
     *
     * @return $this
     */
    public function setForward($forward)
    {
        $this->forward = $forward;

        return $this;
    }

}
