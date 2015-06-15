<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TeaserBundle\AccessControl;

use Phlexible\Bundle\AccessControlBundle\Rights\RightsProviderInterface;

/**
 * Teaser rights provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TeaserRightsProvider implements RightsProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRights()
    {
        return [
            'internal' => [
                'teaser' => [
                    'VIEW' => [
                        'iconCls' => 'p-element-view-icon',
                        'bit'     => 1,
                    ],
                    'EDIT' => [
                        'iconCls' => 'p-element-edit-icon',
                        'bit'     => 2,
                    ],
                    'CREATE' => [
                        'iconCls' => 'p-element-add-icon',
                        'bit'     => 4,
                    ],
                    'DELETE' => [
                        'iconCls' => 'p-element-delete-icon',
                        'bit'     => 8,
                    ],
                    'PUBLISH' => [
                        'iconCls' => 'p-element-publish-icon',
                        'bit'     => 16,
                    ],
                    'ACCESS' => [
                        'iconCls' => 'p-element-tab_rights-icon',
                        'bit'     => 32,
                    ],
                ],
            ],
        ];
    }
}
