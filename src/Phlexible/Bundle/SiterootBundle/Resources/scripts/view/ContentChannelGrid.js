Ext.define('Phlexible.siteroots.ContentChannelGrid', {
    extend: 'Ext.grid.GridPanel',
    alias: 'widget.siteroots-contentchannels',

    title: Phlexible.siteroots.Strings.contentchannels,
    strings: Phlexible.siteroots.Strings,
    iconCls: 'p-contentchannels-component-icon',
    border: false,

    initComponent: function () {
        this.initMyStore();
        this.initMyColumns();
        this.callParent(arguments);
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.siteroots.model.Contentchannel'
        });
    },

    initMyColumns: function() {
        this.columns = [
            {
                header: this.strings.contentchannel_id,
                hidden: true,
                dataIndex: 'contentchannel_id'
            },
            {
                header: this.strings.contentchannel,
                dataIndex: 'contentchannel'
            },
            this.cc1 = new Ext.grid.CheckColumn({
                header: this.strings.active,
                dataIndex: 'used',
                width: 50
            }),
            this.cc2 = new Ext.grid.CheckColumn({
                header: this.strings['default'],
                dataIndex: 'default',
                width: 50
            })
        ];
    },

    /**
     * After the siteroot selection changes load the siteroot data.
     *
     * @param {Number} id
     * @param {String} title
     * @param {Object} data
     */
    loadData: function (id, title, data) {
        this.deletedRecords = [];
        this.store.commitChanges();

        this.store.loadData(data.contentchannels);
    },

    /**
     * Get the data to be saved.
     */
    getSaveData: function () {
        // fetch modified records
        var data = [];
        Ext.each(this.store.getRange(), function (r) {
            data.push(r.data);
        });

        return {
            'contentchannels': data
        };
    }
});
