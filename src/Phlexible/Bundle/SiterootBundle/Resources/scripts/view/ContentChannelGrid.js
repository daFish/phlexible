Ext.define('Phlexible.siteroot.view.ContentChannelGrid', {
    extend: 'Ext.grid.GridPanel',
    alias: 'widget.siteroot-contentchannels',

    title: '_ContentChannelGrid',
    iconCls: 'p-contentchannels-component-icon',
    border: false,

    contentchannelIdText: '_contentchannelIdText',
    contentchannelText: '_contentchannelText',
    activeText: '_activeText',
    defaultText: '_defaultText',

    initComponent: function () {
        this.initMyStore();
        this.initMyColumns();
        this.callParent(arguments);
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.siteroot.model.Contentchannel'
        });
    },

    initMyColumns: function() {
        this.columns = [
            {
                header: this.contentchannelIdText,
                hidden: true,
                dataIndex: 'contentchannel_id'
            },
            {
                header: this.contentchannelText,
                dataIndex: 'contentchannel'
            },
            this.cc1 = new Ext.grid.CheckColumn({
                header: this.activeText,
                dataIndex: 'used',
                width: 50
            }),
            this.cc2 = new Ext.grid.CheckColumn({
                header: this.defaultText,
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
