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
 * Zend lucene docx content extractor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ZendLuceneDocxExtractor implements ExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(ExtendedFileInterface $file, MediaType $mediaType, $targetFormat)
    {
        return $targetFormat === 'text' && $mediaType->getName() === 'docx';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(ExtendedFileInterface $file, MediaType $mediaType, $targetFormat)
    {
        $document = \Zend_Search_Lucene_Document_Docx::loadDocxFile($file->getPhysicalPath());

        // set zend lucene document body as content
        $content = new Content($document->getFieldUtf8Value('body'));

        return $content;
    }

}
