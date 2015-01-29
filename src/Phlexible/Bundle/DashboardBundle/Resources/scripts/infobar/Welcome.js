/**
 * Welcome infobar
 */
Ext.define('Phlexible.dashboard.infobar.Welcome', {
    extend: 'Phlexible.dashboard.infobar.AbstractInfoBar',
    requires: ['Phlexible.dashboard.infobar.AbstractInfoBar'],

    alias: 'widget.dashboard-infobar-welcome',

    welcomeToText: '_welcomeToText',
    addPortletText: '_addPortletText',
    changeLayoutText: '_changeLayoutText',

    initComponent: function() {
        this.data = {
            title: Phlexible.title,
            version: Phlexible.version
        };

        this.callParent(arguments);
    },

    createTpl: function() {
        return new Ext.XTemplate(
            Phlexible.Icon.inlineText('phlexible', this.welcomeToText)
        );
    }
});
