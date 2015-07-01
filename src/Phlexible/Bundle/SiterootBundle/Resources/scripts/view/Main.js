Ext.define('Phlexible.siteroot.view.Main', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Phlexible.siteroot.model.Siteroot',
        'Phlexible.siteroot.view.List',
        'Phlexible.siteroot.view.Navigations',
        'Phlexible.siteroot.view.Properties',
        'Phlexible.siteroot.view.SpecialTids',
        'Phlexible.siteroot.view.Titles',
        'Phlexible.siteroot.view.Urls'
    ],
    xtype: 'siteroot.main',

    iconCls: Phlexible.Icon.get('globe'),
    cls: 'p-siteroot-main',
    border: false,
    layout: 'border',
    referenceHolder: true,
    viewModel: {
        stores: {
            siteroots: {
                model: 'Phlexible.siteroot.model.Siteroot',
                autoLoad: true,
                sorters: [{
                    property: 'title',
                    direction: 'ASC'
                }]
            }
        }
    },

    siterootText: '_siterootText',
    checkAccordionsForErrorsText: '_checkAccordionsForErrorsText',

    /**
     * Fires after the active Siteroot has been changed
     *
     * @event siterootChange
     * @param {Number} siterootId The ID of the selected siteroot.
     * @param {String} siterootTitle The Title of the selected siteroot.
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
            xtype: 'siteroot.list',
            itemId: 'list',
            reference: 'list',
            region: 'west',
            width: 250,
            minWidth: 200,
            maxWidth: 350,
            split: false,
            padding: '5 0 5 5',
            bind: {
                store: '{siteroots}'
            },
            listeners: {
                save: this.onSave,
                siterootDataChange: this.onSiterootDataChange,
                scope: this
            }
        }, {
            xtype: 'panel',
            itemId: 'accordions',
            region: 'center',
            title: this.siterootText,
            layout: 'accordion',
            padding: 5,
            bind: {
                title: '{list.selection.title}',
                disabled: '{!list.selection}'
            },
            items: [
                {
                    xtype: 'siteroot.urls',
                    bind: {
                        store: '{list.selection.urls}'
                    }
                },
                {
                    xtype: 'siteroot.titles'
                },
                {
                    xtype: 'siteroot.properties'
                },
                {
                    xtype: 'siteroot.specialtids',
                    bind: {
                        store: '{list.selection.specialTids}'
                    }
                },
                {
                    xtype: 'siteroot.navigations',
                    bind: {
                        store: '{list.selection.navigations}'
                    }
                }
            ]
        }];
    },

    loadParams: function () {
    },

    /**
     * After the siteroot data changed.
     *  - new siteroot added
     *  - title of siteroot changed
     */
    onSiterootDataChange: function () {
        Phlexible.Frame.loadConfig();
        Phlexible.Frame.menu.load();
    },

    onSave: function() {
        debugger;
        this.getViewModel().getStore('siteroots').sync();
    }
});
