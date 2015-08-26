<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Site\File\Dumper;

use FluentDOM\Document;
use Phlexible\Component\Site\Domain\Site;

/**
 * Dumper interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface DumperInterface
{
    /**
     * @param Site $site
     *
     * @return Document
     */
    public function dump(Site $site);
}
