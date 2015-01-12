Ext.define('Phlexible.gui.bundles.MainPanel', {
    extend: 'Ext.Panel',
    alias: 'widget.gui-bundles',

    title: Phlexible.gui.Strings.bundles,
    strings: Phlexible.gui.Strings,
    iconCls: 'p-gui-manager-icon',
    cls: 'p-gui-bundles',
    closable: true,
    layout: 'border',
    border: false,

    initComponent: function () {
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
                xtype: 'gui-bundles-grid',
                itemId: 'list',
                region: 'center',
                border: true,
                padding: 5
            }
        ];

        this.callParent(arguments);
    },

    loadParams: function () {

    }
});