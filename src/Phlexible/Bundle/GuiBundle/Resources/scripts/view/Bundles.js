Ext.define('Phlexible.gui.view.Bundles', {
    extend: 'Ext.panel.Panel',
    xtype: 'gui.bundles',
    requires: [
        'Phlexible.gui.model.Bundle',
        'Phlexible.gui.view.BundlesController'
    ],

    controller: 'gui.bundles',

    iconCls: Phlexible.Icon.get('symfony'),
    cls: 'p-gui-bundles',
    closable: true,
    layout: 'border',
    border: false,

    bundleText: '_bundleText',
    nameText: '_nameText',
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
                    url: Phlexible.Router.generate('phlexible_api_gui_get_bundles'),
                    reader: {
                        type: 'json',
                        idProperty: 'name',
                        rootProperty: 'bundles',
                        totalProperty: 'count'
                    },
                },
                autoLoad: false,
                sorters: [{
                    property: 'name',
                    direction: 'ASC'
                }]
            }),
            columns: [{
                header: this.nameText,
                width: 250,
                dataIndex: 'name',
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
