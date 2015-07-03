Ext.define('Phlexible.message.portlet.Messages', {
    extend: 'Ext.dashboard.Panel',
    requires: ['Phlexible.message.model.Message'],
    alias: 'widget.messages-portlet',

    iconCls: Phlexible.Icon.get('application-list'),
    extraCls: 'p-message-portlet-messages',
    layout: 'fit',

    imageUrl: '/bundles/phlexiblemessage/images/portlet-messages.png',
    maxItems: 20,

    noRecentMessagesText: '_noRecentMessagesText',
    typeText: '_typeText',
    subjectText: '_subjectText',
    channelText: '_channelText',
    dateText: '_dateText',

    initComponent: function () {
        this.items = [{
            xtype: 'grid',
            sortableColumns: false,
            enableColumnHide: false,
            emptyText: this.noRecentMessagesText,
            viewConfig: {
                deferEmptyText: false
            },
            store: Ext.create('Ext.data.Store', {
                model: 'Phlexible.message.model.Message',
                id: 'id',
                sorters: [{property: 'time', direction: 'DESC'}]
            }),
            columns: [{
                header: this.subjectText,
                dataIndex: 'subject',
                flex: 1
            },{
                header: this.channelText,
                dataIndex: 'channel',
                width: 80
            },{
                header: this.typeText,
                dataIndex: 'type',
                width: 80,
                renderer: function(v) {
                    var type = Phlexible.message.TypeNames[v];
                    return '<span class="p-label p-label-message-' + type + '">' + type + '</span>';
                }
            },{
                xtype: 'datecolumn',
                header: this.dateText,
                dataIndex: 'createdAt',
                format: 'Y-m-d H:i:s',
                width: 120
            }]
        }];

        this.callParent(arguments);
    },

    updateData: function (data) {
        var store = this.getComponent(0).getStore();

        store.beginUpdate();

        Ext.each(data, function(message) {
            if (store.find('id', message.id) === -1) {
                store.add(Ext.create('Phlexible.message.model.Message', message));
            }
        });

        store.sort('createdAt', 'DESC');

        while (store.count() > this.maxItems) {
            store.removeAt(this.maxItems);
        }

        store.endUpdate();
    }
});
