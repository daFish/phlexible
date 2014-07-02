<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ContentchannelBundle\AclProvider;

use Phlexible\Bundle\SecurityBundle\Acl\AclProvider\AclProvider;

/**
 * Contentchannel acl provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ContentchannelAclProvider extends AclProvider
{
    /**
     * {@inheritdoc}
     */
    public function provideResources()
    {
        return array(
            'contentchannels',
        );
    }
}