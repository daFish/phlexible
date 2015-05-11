Ext.define('Phlexible.gui.portlet.Load', {
    extend: 'Ext.dashboard.Panel',
    requires: ['Phlexible.gui.model.LoadEntry'],

    xtype: 'gui-load-portlet',

    iconCls: Phlexible.Icon.get('system-monitor'),
    bodyPadding: 5,

    COLOR1: '#eacc00',
    COLOR5: '#ea8f00',
    COLOR15: '#b1441e',
    imageUrl: '/bundles/phlexiblegui/images/portlet-load.png',

    height: 200,

    oneMinAvgText: '_oneMinAvgText',
    fiveMinAvgText: '_fiveMinAvgText',
    fifteenMinAvgText: '_fifteenMinAvgText',
    waitingForDataText: '_waitingForDataText',

    initComponent: function () {
//        if (Phlexible.StartMessage) {
//            Phlexible.StartMessage.on('task', this.processMessage, this);
//        }

        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.gui.model.LoadEntry',
            data: this.data
        });

        this.items = [
            {
                xtype: 'sparklineline',
                values: [1,5,4,3,4,5,6,3,4,5,6],
                height: 25
            },
            {
                hidden: true,
                data: this.data,
                tpl: new Ext.XTemplate(
                    '<table>',
                    '<tr>',
                    '<tpl if="!Ext.isIE"><td><div style="background-color: ' + this.COLOR1 + '; width: 10px; height: 8px; border: 1px solid black;" /></td></tpl>',
                    '<td>' + this.oneMinAvgText + ':</td>',
                    '<td>{[values.l1.toFixed(2)]}</td>',
                    '</tr>',
                    '<tr>',
                    '<tpl if="!Ext.isIE"><td><div style="background-color: ' + this.COLOR5 + '; width: 10px; height: 8px; border: 1px solid black;" /></td></tpl>',
                    '<td>' + this.fiveMinAvgText + ':</td>',
                    '<td>{[values.l5.toFixed(2)]}</td>',
                    '</tr>',
                    '<tr>',
                    '<tpl if="!Ext.isIE"><td><div style="background-color: ' + this.COLOR15 + '; width: 10px; height: 8px; border: 1px solid black;" /></td></tpl>',
                    '<td>' + this.fifteenMinAvgText + ':</td>',
                    '<td>{[values.l15.toFixed(2)]}</td>',
                    '</tr>',
                    '</table>'
                )
            }
        ];

        delete this.data;

        this.callParent(arguments);
    },

    updateData: function (data) {
        var r = new Phlexible.gui.model.LoadEntry({l1: data[0], l5: data[1], l15: data[2]});
        this.store.add(r);

    }
});
