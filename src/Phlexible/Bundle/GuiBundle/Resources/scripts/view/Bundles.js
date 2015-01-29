Ext.define('Phlexible.gui.view.Bundles', {
    extend: 'Ext.panel.Panel',
    xtype: 'gui.bundles',
    requires: [
        'Phlexible.gui.view.BundlesController'
    ],

    controller: 'gui.bundles',

    iconCls: Phlexible.Icon.get('symfony'),
    cls: 'p-gui-bundles',
    closable: true,
    layout: 'border',
    border: false,

    bundleText: '_bundleText',
    packageText: '_packageText',
    classnameText: '_classnameText',
    pathText: '_pathText',
    resetText: '_resetTest',

    initComponent: function() {
        this.items = [{
            xtype: 'form',
            region: 'west',
            itemId: 'filter',
            width: 200,
            padding: '5 0 5 5',
            bodyPadding: 5,
            header: false,
            items: [{
                xtype: 'panel',
                title: this.bundleText,
                layout: 'form',
                frame: true,
                defaults: {
                    hideLabel: true
                },
                items: [{
                    xtype: 'textfield',
                    name: 'filter',
                    flex: 1,
                    enableKeyEvents: true,
                    listeners: {
                        keyup: 'onKeyup'
                    }
                }]
            },{
                xtype: 'panel',
                itemId: 'packages',
                title: this.packageText,
                margin: '5 0 0 0',
                layout: 'form',
                frame: true,
                collapsible: true,
                defaults: {
                    hideLabel: true
                },
                items: [{
                    border: false,
                    plain: true,
                    frame: false,
                    html: '<div class="loading-indicator">Loading...</div>'
                }]
            }],
            dockedItems: [{
                xtype: 'toolbar',
                itemId: 'buttons',
                dock: 'bottom',
                ui: 'footer',
                items: [
                    '->',
                {
                    xtype: 'button',
                    itemId: 'resetBtn',
                    text: this.resetText,
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.RELOAD),
                    disabled: true,
                    handler: 'onResetFilter'
                }]
            }]
        }, {
            xtype: 'grid',
            region: 'center',
            itemId: 'list',
            padding: 5,
            border: true,
            loadMask: true,
            store: Ext.create('Ext.data.Store', {
                model: 'Phlexible.gui.model.Bundle',
                proxy: {
                    type: 'ajax',
                    url: Phlexible.Router.generate('gui_bundles'),
                    reader: {
                        type: 'json',
                        idProperty: 'id',
                        totalProperty: 'count'
                    }
                },
                // TODO: enable when buffered paging reload works. disabled for now.
                autoLoad: true,
                sorters: [{
                    property: 'id',
                    direction: 'ASC'
                }]
            }),
            columns: [{
                header: this.bundleText,
                width: 250,
                dataIndex: 'id',
                resizable: false
            }, {
                header: this.packageText,
                width: 100,
                dataIndex: 'package',
                resizable: false
            }, {
                header: this.classnameText,
                width: 400,
                dataIndex: 'classname',
                resizable: false,
                flex: 1
            }, {
                header: this.pathText,
                width: 500,
                dataIndex: 'path',
                hidden: true,
                resizable: false
            }]
        }];

        this.callParent(arguments);
    }
});