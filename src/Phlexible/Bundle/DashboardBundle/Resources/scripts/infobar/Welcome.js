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

        this.on({
            afterrender: function(c) {
                var addBtn = this.el.down('.add-button');

                addBtn.on({
                    click: function() {
                        this.fireEvent('addPortlet');
                    },
                    scope: this
                });
            }
        });
    },

    createTpl: function() {
        return new Ext.XTemplate(
            '<div style="float: right">',
            '<span class="add-button" style="padding-left: 10px; cursor: pointer;">',
            Phlexible.Icon.inline(Phlexible.Icon.ADD) + ' ' + this.addPortletText,
            '</span>',
            '</div>',
            Phlexible.Icon.inlineText('phlexible', this.welcomeToText)
        );
    }
});