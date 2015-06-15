<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaExtractor\ContentExtractor;

use Phlexible\Component\MediaExtractor\Extractor\ExtractorInterface;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaType\Model\MediaType;

/**
 * Zend lucene pptx content extractor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ZendLucenePptxExtractor implements ExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(ExtendedFileInterface $file, MediaType $mediaType, $targetFormat)
    {
        return $targetFormat === 'text' && $mediaType->getName() === 'pptx';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(ExtendedFileInterface $file, MediaType $mediaType, $targetFormat)
    {
        $document = \Zend_Search_Lucene_Document_Pptx::loadPptxFile($file->getPhysicalPath());

        // set zend lucene document body as content
        $content = new Content($document->getFieldUtf8Value('body'));

        return $content;
    }

}
