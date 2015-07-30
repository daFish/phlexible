<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle;

use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\ElementBundle\Entity\ElementSource;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementBundle\Model\ElementManagerInterface;
use Phlexible\Bundle\ElementBundle\Model\ElementSourceManagerInterface;
use Phlexible\Bundle\ElementBundle\Model\ElementVersionManagerInterface;
use Phlexible\Component\Elementtype\Domain\Elementtype;
use Phlexible\Component\Elementtype\File\Dumper\XmlDumper;
use Phlexible\Component\Elementtype\Model\ViabilityManagerInterface;

/**
 * Element service
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementService
{
    /**
     * @var ElementManagerInterface
     */
    private $elementManager;

    /**
     * @var ElementVersionManagerInterface
     */
    private $elementVersionManager;

    /**
     * @var ElementSourceManagerInterface
     */
    private $elementSourceManager;

    /**
     * @var ViabilityManagerInterface
     */
    private $viabilityManager;

    /**
     * @param ElementManagerInterface        $elementManager
     * @param ElementVersionManagerInterface $elementVersionManager
     * @param ElementSourceManagerInterface  $elementSourceManager
     * @param ViabilityManagerInterface      $viabilityManager
     */
    public function __construct(
        ElementManagerInterface $elementManager,
        ElementVersionManagerInterface $elementVersionManager,
        ElementSourceManagerInterface $elementSourceManager,
        ViabilityManagerInterface $viabilityManager
    ) {
        $this->elementManager = $elementManager;
        $this->elementVersionManager = $elementVersionManager;
        $this->elementSourceManager = $elementSourceManager;
        $this->viabilityManager = $viabilityManager;
    }

    /**
     * Find element by ID
     *
     * @param int $eid
     *
     * @return Element
     */
    public function findElement($eid)
    {
        return $this->elementManager->find($eid);
    }

    /**
     * @param Element $element
     * @param int     $version
     *
     * @return ElementVersion
     */
    public function findElementVersion(Element $element, $version)
    {
        $elementVersion = $this->elementVersionManager->find($element, $version);

        return $elementVersion;
    }

    /**
     * @param Element $element
     *
     * @return array
     */
    public function getVersions(Element $element)
    {
        return $this->elementVersionManager->getVersions($element);
    }

    /**
     * @param Element $element
     *
     * @return \Phlexible\Component\Elementtype\Domain\Elementtype
     */
    public function findElementtype(Element $element)
    {
        return $this->elementSourceManager->findElementtype($element->getElementtypeId());
    }

    /**
     * @param string $elementtypeId
     *
     * @return ElementSource
     */
    public function findElementSource($elementtypeId)
    {
        return $this->elementSourceManager->findElementSource($elementtypeId);
    }

    /**
     * @param \Phlexible\Component\Elementtype\Domain\Elementtype $elementtype
     *
     * @return ElementVersion[]
     */
    public function findOutdatedElementSources(Elementtype $elementtype)
    {
        return $this->elementSourceManager->findOutdatedElementSources($elementtype);
    }

    /**
     * @param \Phlexible\Component\Elementtype\Domain\Elementtype $elementtype
     *
     * @return Elementtype[]
     */
    public function findAllowedParents(Elementtype $elementtype)
    {
        $elementtypes = array();
        foreach ($this->viabilityManager->findAllowedParents($elementtype) as $viability) {
            $elementtypes[] = $this->elementSourceManager->findElementtype($viability->getUnderElementtypeId());
        }

        return $elementtypes;
    }

    /**
     * @param \Phlexible\Component\Elementtype\Domain\Elementtype $elementtype
     *
     * @return \Phlexible\Component\Elementtype\Domain\Elementtype[]
     */
    public function findAllowedChildren(Elementtype $elementtype)
    {
        $elementtypes = array();
        foreach ($this->viabilityManager->findAllowedChildren($elementtype) as $viability) {
            $viabilityElementtype = $this->elementSourceManager->findElementtype($viability->getElementtypeId());
            if ($viabilityElementtype) {
                $elementtypes[] = $viabilityElementtype;
            }
        }

        return $elementtypes;
    }

    /**
     * @param ElementSource $elementSource
     * @param string        $masterLanguage
     * @param string        $userId
     *
     * @return ElementVersion
     */
    public function createElement(ElementSource $elementSource, $masterLanguage, $userId)
    {
        $element = new Element();
        $element
            ->setElementtypeId($elementSource->getElementtypeId())
            ->setMasterLanguage($masterLanguage)
            ->setLatestVersion(1)
            ->setCreateUserId($userId)
            ->setCreatedAt(new \DateTime());

        $elementVersion = new ElementVersion();
        $elementVersion
            ->setVersion(1)
            ->setElement($element)
            ->setElementSource($elementSource)
            ->setCreateUserId($userId)
            ->setCreatedAt(new \DateTime());

        $this->elementManager->updateElement($element, false);
        $this->elementVersionManager->updateElementVersion($elementVersion);

        return $elementVersion;
    }

    /**
     * @param Element $element
     * @param array   $content
     * @param string  $triggerLanguage
     * @param string  $userId
     * @param string  $comment
     *
     * @return ElementVersion
     */
    public function createElementVersion(Element $element, array $content, $triggerLanguage, $userId, $comment = null)
    {
        $oldElementVersion = $this->findElementVersion($element, $element->getLatestVersion());

        $elementSource = $this->findElementSource($element->getElementtypeId());

        $elementVersion = clone $oldElementVersion;
        $elementVersion
            ->setId(null)
            ->setElement($element)
            ->setElementSource($elementSource)
            ->setVersion($oldElementVersion->getVersion() + 1)
            ->setCreateUserId($userId)
            ->setCreatedAt(new \DateTime())
            ->setComment($comment)
            ->setContent($content ?: null)
            ->setTriggerLanguage($triggerLanguage);

        $elementVersion
            ->getMappedFields()->clear();

        $element->setLatestVersion($elementVersion->getVersion());

        $this->elementManager->updateElement($element, false);

        if ($content) {
            die("content");
        }

        $this->elementVersionManager->updateElementVersion($elementVersion, true);

        return $elementVersion;
    }

    /**
     * @param \Phlexible\Component\Elementtype\Domain\Elementtype $elementtype
     *
     * @return ElementSource
     */
    public function createElementSource(Elementtype $elementtype)
    {
        $xmlDumper = new XmlDumper();

        $elementSource = new ElementSource();
        $elementSource
            ->setElementtypeId($elementtype->getId())
            ->setElementtypeRevision($elementtype->getRevision())
            ->setType($elementtype->getType())
            ->setXml($xmlDumper->dump($elementtype))
            ->setImportedAt(new \DateTime());

        $this->elementSourceManager->updateElementSource($elementSource);

        return $elementSource;
    }

    /**
     * @param Element $element
     */
    public function deleteElement(Element $element)
    {
        $this->elementManager->deleteElement($element);
    }
}
