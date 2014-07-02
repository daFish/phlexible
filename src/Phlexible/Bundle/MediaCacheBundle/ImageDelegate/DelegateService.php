<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\ImageDelegate;

use Phlexible\Bundle\DocumenttypeBundle\Model\Documenttype;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateInterface;

/**
 * Delegate worker
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DelegateService
{
    /**
     * @var DelegateWorker
     */
    private $worker;

    /**
     * @param DelegateWorker $worker
     */
    public function __construct(DelegateWorker $worker)
    {
        $this->worker = $worker;
    }

    /**
     * @return DelegateWorker
     */
    public function getWorker()
    {
        return $this->worker;
    }

    /**
     * @param TemplateInterface $template
     * @param Documenttype      $documentType
     * @param bool              $createOnDemand
     *
     * @return string
     */
    public function getClean(TemplateInterface $template, Documenttype $documentType, $createOnDemand = true)
    {
        $filename = $this->worker->getCleanFilename($template, $documentType);

        if (file_exists($filename)) {
            return $filename;
        }

        if (!$createOnDemand) {
            return null;
        }

        $this->worker->write($documentType, $template);

        return $filename;
    }

    /**
     * @param TemplateInterface $template
     * @param Documenttype      $documentType
     * @param bool              $createOnDemand
     *
     * @return string
     */
    public function getWaiting(TemplateInterface $template, Documenttype $documentType, $createOnDemand = true)
    {
        $filename = $this->worker->getWaitingFilename($template, $documentType);

        if (file_exists($filename)) {
            return $filename;
        }

        if (!$createOnDemand) {
            return null;
        }

        $this->worker->write($documentType, $template);

        return $filename;
    }
}