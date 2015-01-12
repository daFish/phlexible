/**
 * Welcome infobar
 */
Ext.define('Phlexible.dashboard.infobar.Welcome', {
    extend: 'Phlexible.dashboard.infobar.AbstractInfoBar',
    alias: 'widget.dashboard-infobar-welcome',

    welcomeToText: '_welcome_to_{title}_{values.version}',
    addPortletText: '_add_portlet',
    changeLayoutText: '_change_layout',

    initComponent: function() {
        this.data = {
            title: Phlexible.App.getConfig().get('app.title'),
            version: Phlexible.App.getConfig().get('app.version')
        };

        this.callParent(arguments);

        this.on({
            afterrender: function(c) {
                var addBtn = this.el.down('.add-button'),
                    columnsBtn = this.el.down('.columns-button');

                addBtn.on({
                    click: function() {
                        this.fireEvent('addPortlet');
                    },
                    scope: this
                });

                columnsBtn.on({
                    click: function() {
                        this.fireEvent('editColumns');

                    },
                    scope:  this
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
                '<span class="columns-button" style="padding-left: 10px; cursor: pointer;">',
                    Phlexible.Icon.inline('edit-column') + ' ' + this.changeLayoutText,
                '</span>',
            '</div>',
            Phlexible.Icon.inlineDirect('p-icon-phlexible') + ' ' + this.welcomeToText
        );
    }
});
