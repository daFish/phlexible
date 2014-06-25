<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Portlet;

use Phlexible\Bundle\DashboardBundle\Portlet\AbstractPortlet;
use Phlexible\Bundle\MediaCacheBundle\Model\CacheManagerInterface;
use Phlexible\Bundle\MediaSiteBundle\Site\SiteManager;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Latest files portlet
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LatestFilesPortlet extends AbstractPortlet
{
    /**
     * @var SiteManager
     */
    private $siteManager;

    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @var string
     */
    private $style;

    /**
     * @var integer
     */
    private $numItems;

    /**
     * @param TranslatorInterface      $translator
     * @param SiteManager              $siteManager
     * @param CacheManagerInterface    $cacheManager
     * @param SecurityContextInterface $securityContext
     * @param string                   $style
     * @param integer                  $numItems
     */
    public function __construct(TranslatorInterface $translator,
                                SiteManager $siteManager,
                                CacheManagerInterface $cacheManager,
                                SecurityContextInterface $securityContext,
                                $style,
                                $numItems)
    {
        $this->id        = 'mediamanager-portlet';
        $this->title     = $translator->trans('mediamanager.latest_files', array(), 'gui');
        $this->class     = 'Phlexible.mediamanager.portlet.LatestFiles';
        $this->iconClass = 'p-mediamanager-portlet-icon';
        $this->resource  = 'media';

        $this->siteManager = $siteManager;
        $this->cacheManager = $cacheManager;
        $this->securityContext = $securityContext;
        $this->style = $style;
        $this->numItems = $numItems;
    }
    /**
     * Return settings
     *
     * @return array
     */
    public function getSettings()
    {
        return array(
            'style' => $this->style
        );
    }

    /**
     * Return Portlet data
     *
     * @return array
     */
    public function getData()
    {
        $data = array();

        try {
            $sites = $this->siteManager->getAll();
            $site  = current($sites);
            $files = $site->findLatestFiles($this->numItems);

            foreach ($files as $file) {
                $folder = $site->findFolder($file->getFolderId());

                if (!$this->securityContext->isGranted('FILE_READ', $folder)) {
                    continue;
                }

                $cacheItems = $this->cacheManager->findByFile($file);
                $cacheStatus = array();
                foreach ($cacheItems as $cacheItem) {
                    $cacheStatus[$cacheItem->getTemplateKey()] = $cacheItem->getStatus() . ';' . $cacheItem->getCreatedAt()->format('YmdHis');
                }

                $data[] = array(
                    'id'                => sprintf('%s___%s', $file->getId(), $file->getVersion()),
                    'file_id'           => $file->getId(),
                    'file_version'      => $file->getVersion(),
                    'folder_id'         => $file->getFolderId(),
                    'folder_path'       => $folder->getIdPath(),
                    'document_type_key' => strtolower($file->getAttribute('documenttype')),
                    'time'              => $file->getCreatedAt()->format('U'),
                    'title'             => $file->getName(),
                    'cache'             => $cacheStatus
                );
            }
        } catch (\Exception $e) {
            $data = array();
        }

        return $data;
    }
}
