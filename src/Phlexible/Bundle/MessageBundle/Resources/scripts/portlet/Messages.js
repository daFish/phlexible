Ext.define('Phlexible.message.portlet.model.Message', {
    extend: 'Ext.data.Model',

    fields: [
        {name: 'id', type: 'string'},
        {name: 'subject', type: 'string'},
        {name: 'time', type: 'date'}
    ]
});

Ext.define('Phlexible.message.portlet.Messages', {
    extend: 'Ext.dashboard.Panel',
    alias: 'widget.messages-portlet',

    iconCls: Phlexible.Icon.get('application-list'),
    extraCls: 'messages-portlet',
    bodyStyle: 'padding: 5px',

    imageUrl: '/bundles/phlexiblemessage/images/portlet-messages.png',

    noRecentMessagesText: '_noRecentMessagesText',
    priorityText: '_priorityText',
    typeText: '_typeText',
    subjectText: '_subjectText',
    channelText: '_channelText',
    dateText: '_dateText',

    initComponent: function () {
        this.tpl = new Ext.XTemplate(
            '<table width="100%">',
            '<colgroup>',
            '<col />',
            '<col width="20" />',
            '<col width="20" />',
            '<col width="20" />',
            '<col width="110" />',
            '</colgroup>',
            '<tr>',
            '<th>' + this.subjectText + '</th>',
            '<th>' + this.channelText + '</th>',
            '<th style="text-align: center;" qtip="' + this.priorityText + '">' + this.priorityText.substring(0, 1) + '.</th>',
            '<th style="text-align: center;" qtip="' + this.typeText + '">' + this.typeText.substring(0, 1) + '.</th>',
            '<th>' + this.dateText + '</th>',
            '</tr>',
            '<tpl for=".">',
            '<tr class="messages-wrap" id="message_{id}">',
            '<td style="vertical-align: middle;" class="messages-subject">{subject}</td>',
            '<td style="vertical-align: middle;" class="messages-icon">{channel}</td>',
            '<td style="vertical-align: middle; text-align: center;" class="messages-icon">{[Phlexible.inlineIcon("p-message-priority_" + values.priority + "-icon")]}</td>',
            '<td style="vertical-align: middle; text-align: center;" class="messages-icon">{[Phlexible.inlineIcon("p-message-type_" + values.type + "-icon")]}</td>',
            '<td style="vertical-align: middle;" class="messages-date">{time:date("Y-m-d H:i:s")}</td>',
            '</tr>',
            '</tpl>',
            '</table>'
        );

        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.message.portlet.model.Message',
            id: 'id',
            sorters: [{property: 'time', direction: 'DESC'}],
            data: this.item.data
        });

        this.items = [
            {
                xtype: 'dataview',
                itemSelector: 'tr.messages-wrap',
                overItemClass: 'messages-wrap-over',
                style: 'overflow: auto',
                singleSelect: true,
                emptyText: this.noRecentMessagesText,
                deferEmptyText: false,
                autoHeight: true,
                store: this.store,
                tpl: this.tpl,
                listeners: {
                    click: function (c, index, node) {
                        return;
                    },
                    scope: this
                }
            }
        ];

        this.callParent(arguments);
    },

    updateData: function (data) {
        var latestMessagesMap = [];

        for (var i = data.length - 1; i >= 0; i--) {
            var row = data[i];
            latestMessagesMap.push(row.id);
            var r = this.store.getById(row.id);
            if (!r) {
                row.time = new Date(row.time * 1000);
                this.store.insert(0, new Phlexible.message.portlet.Message(row, row.id));

                Ext.fly('message_' + row.id).frame('#8db2e3', 1);
            }
        }

        for (var i = this.store.getCount() - 1; i >= 0; i--) {
            var r = this.store.getAt(i);
            if (latestMessagesMap.indexOf(r.id) == -1) {
                this.store.remove(r);
            }
        }

        this.store.sort('time', 'DESC');
    }
});
