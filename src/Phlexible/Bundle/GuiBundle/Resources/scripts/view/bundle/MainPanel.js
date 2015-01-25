Ext.define('Phlexible.gui.view.bundle.MainPanel', {
    extend: 'Ext.Panel',
    alias: 'widget.gui-bundles',

    iconCls: Phlexible.Icon.get('symfony'),
    cls: 'p-gui-bundles',
    closable: true,
    layout: 'border',
    border: false,

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'gui-bundles-filter',
                region: 'west',
                width: 200,
                padding: '5 0 5 5',
                header: false,
                listeners: {
                    updateFilter: function (data) {
                        this.getComponent('list').setFilterData(data);
                    },
                    scope: this
                }
            },
            {
                xtype: 'gui-bundles-list',
                itemId: 'list',
                region: 'center',
                border: true,
                padding: 5
            }
        ];
    },

    loadParams: function () {

    }
});