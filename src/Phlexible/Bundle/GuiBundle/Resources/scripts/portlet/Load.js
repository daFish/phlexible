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

    height: 100,
    layout: 'vbox',

    oneMinAvgText: '_oneMinAvgText',
    fiveMinAvgText: '_fiveMinAvgText',
    fifteenMinAvgText: '_fifteenMinAvgText',
    waitingForDataText: '_waitingForDataText',

    initComponent: function () {
//        if (Phlexible.StartMessage) {
//            Phlexible.StartMessage.on('task', this.processMessage, this);
//        }

        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.gui.model.LoadEntry'
        });

        this.items = [
            {
                itemId: 'spark',
                xtype: 'sparklineline',
                border: true,
                values: [],
                height: 25,
                width: 200,
                fillColor: this.COLOR1,
                lineColor: this.COLOR5
            },{
                xtype: 'component',
                itemId: 'text',
                height: 50,
                data: {l1: 1, l5: 5, l15: 15, ts: 'ts'},
                tpl: new Ext.XTemplate(
                    '<div>',
                    '<span>' + this.oneMinAvgText + ': {[values.l1.toFixed(2)]}</span>',
                    '<span>' + this.fiveMinAvgText + ': {[values.l5.toFixed(2)]}</span>',
                    '<span>' + this.fifteenMinAvgText + ': {[values.l15.toFixed(2)]}</span>',
                    '<span>{samples} Samples</span>',
                    '</div>'
                )
            }
        ];

        delete this.data;

        this.callParent(arguments);

        this.on({
            render: this.fixSparklineWidth,
            resize: this.fixSparklineWidth,
            scope: this
        });
    },

    fixSparklineWidth: function() {
        this.getComponent('spark').setWidth(this.getWidth() - 30);
    },

    updateData: function (data) {
        var entry = Ext.create('Phlexible.gui.model.LoadEntry', {l1: data.l1, l5: data.l5, l15: data.l15, ts: data.ts});
        this.store.add(entry);
        var values = [];
        this.store.each(function(entry) {
            values.push(entry.get('l1'));
        });
        entry.samples = this.store.count();
        this.getComponent('text').setData(Ext.apply({samples: this.store.count()}, entry.data));
        this.getComponent('spark').setValues(values);
    }
});
