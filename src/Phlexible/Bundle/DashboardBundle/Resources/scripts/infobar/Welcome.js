/**
 * Welcome infobar
 */
Ext.define('Phlexible.dashboard.infobar.Welcome', {
    extend: 'Ext.toolbar.Toolbar',

    alias: 'widget.dashboard-infobar-welcome',

    addPortletText: '_addPortletText',

    initComponent: function() {
        this.items = ['->',{
            xtype: 'tbtext',
            html: Phlexible.Icon.inlineText('phlexible', '<b>' + Phlexible.title + ' ' + Phlexible.version + '</b>')
        }, '->', {
            text: this.addPortletText,
            iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
            handler: function() {
                this.fireEvent('addPortlet');
            },
            scope: this
        }];

        this.callParent(arguments);
    }
});
