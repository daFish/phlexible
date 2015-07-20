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
 * Page node
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 */
class PageNode extends Node implements PageInterface
{
}
