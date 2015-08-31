Ext.define('Phlexible.site.view.Main', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Phlexible.site.model.Site',
        'Phlexible.site.view.List',
        'Phlexible.site.view.EntryPoints',
        'Phlexible.site.view.Navigations',
        'Phlexible.site.view.Properties',
        'Phlexible.site.view.NodeAliases',
        'Phlexible.site.view.NodeConstraints',
        'Phlexible.site.view.Site'
    ],
    xtype: 'site.main',

    iconCls: Phlexible.Icon.get('globe'),
    cls: 'p-site-main',
    border: false,
    layout: 'border',
    referenceHolder: true,
    viewModel: {
        stores: {
            sites: {
                model: 'Phlexible.site.model.Site',
                autoLoad: true,
                sorters: [{
                    property: 'title',
                    direction: 'ASC'
                }]
            }
        }
    },

    siteText: '_siteText',
    checkAccordionsForErrorsText: '_checkAccordionsForErrorsText',

    /**
     * Fires after the active Site has been changed
     *
     * @event siteChange
     * @param {Number} siteId The ID of the selected site.
     * @param {String} siteTitle The Title of the selected site.
     */

    /**
     * @private
     */
    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [{
            xtype: 'site.list',
            itemId: 'list',
            reference: 'list',
            region: 'west',
            width: 250,
            minWidth: 200,
            maxWidth: 350,
            split: false,
            padding: '5 0 5 5',
            bind: {
                store: '{sites}'
            },
            listeners: {
                save: this.onSave,
                siteDataChange: this.onSiteDataChange,
                scope: this
            }
        }, {
            xtype: 'panel',
            itemId: 'accordions',
            region: 'center',
            title: this.siteText,
            layout: 'accordion',
            padding: 5,
            bind: {
                title: '{list.selection.title}',
                disabled: '{!list.selection}'
            },
            items: [
                {
                    xtype: 'site.site'
                },
                {
                    xtype: 'site.entry-points',
                    bind: {
                        store: '{list.selection.entryPoints}'
                    }
                },
                {
                    xtype: 'site.properties'
                },
                {
                    xtype: 'site.node-aliases',
                    bind: {
                        store: '{list.selection.nodeAliases}'
                    }
                },
                {
                    xtype: 'site.navigations',
                    bind: {
                        store: '{list.selection.navigations}'
                    }
                },
                {
                    xtype: 'site.node-constraints',
                    bind: {
                        store: '{list.selection.nodeConstraints}'
                    }
                }
            ]
        }];
    },

    loadParams: function () {
    },

    /**
     * After the site data changed.
     *  - new site added
     *  - title of site changed
     */
    onSiteDataChange: function () {
        Phlexible.Frame.loadConfig();
        Phlexible.Frame.menu.load();
    },

    onSave: function() {
        this.getViewModel().getStore('sites').sync();
    }
});
