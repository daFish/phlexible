<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Site;

/**
 * Site events
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiteEvents
{
    /**
     * Fired before a site is created.
     */
    const BEFORE_CREATE_SITE = 'phlexible_site.before_create_site';

    /**
     * Fired after a site has been created.
     */
    const CREATE_SITE = 'phlexible_site.create_site';

    /**
     * Fired before a site is updates.
     */
    const BEFORE_UPDATE_SITE = 'phlexible_site.before_update_site';

    /**
     * Fired after a site has been updated.
     */
    const UPDATE_SITE = 'phlexible_site.update_site';

    /**
     * Fired before a site is saved.
     */
    const BEFORE_SAVE_SITE = 'phlexible_site.before_save_site';

    /**
     * Fired after a site has been saved.
     */
    const SAVE_SITE = 'phlexible_site.save_site';

    /**
     * Fired before a site is deleted.
     */
    const BEFORE_DELETE_SITE = 'phlexible_site.before_delete_site';

    /**
     * Fired after a site has been deleted.
     */
    const DELETE_SITE = 'phlexible_site.delete_site';
}
