Ext.define('Phlexible.message.view.list.MessagesGrid', {
    extend: 'Ext.grid.GridPanel',
    alias: 'widget.message-list-list',

    loadMask: true,
    emptyText: '_emptyText',
    viewConfig: {
        deferEmptyText: false
    },

    displayMessageText: '_displayMessageText',
    emptyMessageText: '_emptyMessageText',
    idText: '_idText',
    subjectText: '_subjectText',
    priorityText: '_priorityText',
    typeText: '_typeText',
    channelText: '_channelText',
    roleText: '_roleText',
    userText: '_userText',
    createdAtText: '_createdAtText',
    priorityUrgent: '_priorityUrgent',
    priorityHighText: '_priorityHighText',
    priorityNormalText: '_priorityNormalText',
    priorityLowText: '_priorityLowText',
    typeInfoText: '_typeInfoText',
    typeErrorText: '_typeErrorText',

    initComponent: function () {
        console.log(this.autoLoad);
        this.autoLoad = this.autoLoad !== false;

        this.initMyStore();
        this.initMyColumns();
        this.initMyPlugins();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.message.model.Message',
            proxy: {
                type: 'ajax',
                url: Phlexible.Router.generate('messages_messages'),
                simpleSortMode: true,
                reader: {
                    type: 'json',
                    rootProperty: 'messages',
                    idProperty: 'id',
                    totalProperty: 'totalCount'
                },
                extraParams: {filter: ''}
            },
            pageSize: 25,
            autoLoad: this.autoLoad,
            sorters: [{property: 'createdAt', direction: 'DESC'}],
            remoteSort: true,
            listeners: {
                load: function (store) {
                    this.fireEvent('messages', this, store.getProxy().getReader().rawData);
                },
                scope: this
            }
        });
    },

    initMyColumns: function() {
        this.columns = [
            {
                header: this.idText,
                dataIndex: 'id',
                sortable: false,
                hidden: true,
                width: 250
            }, {
                header: this.subjectText,
                dataIndex: 'subject',
                sortable: true,
                flex: 1
            }, {
                header: this.priorityText,
                dataIndex: 'priority',
                sortable: true,
                width: 70,
                renderer: function (s) {
                    return s ? Phlexible.Icon.inlineText(Phlexible.message.PriorityIcons[s], this['priority' + Ext.String.capitalize(s) + 'Text']) : '';
                }
            }, {
                header: this.typeText,
                dataIndex: 'type',
                sortable: true,
                width: 70,
                renderer: function (s) {
                    return s ? Phlexible.Icon.inlineText(Phlexible.message.TypeIcons[s], this['type' + Ext.String.capitalize(s) + 'Text']) : '';
                }
            }, {
                header: this.channelText,
                dataIndex: 'channel',
                sortable: true,
                width: 100
            }, {
                header: this.roleText,
                dataIndex: 'role',
                sortable: true,
                width: 100
            }, {
                header: this.userText,
                dataIndex: 'user',
                sortable: true,
                width: 130
            }, {
                header: this.createdAtText,
                dataIndex: 'createdAt',
                sortable: true,
                width: 130
            }
        ];
    },

    initMyPlugins: function() {
        this.plugins = [{
            ptype: 'rowexpander',
            rowBodyTpl: new Ext.XTemplate(
                '<p style="padding: 0 10px 10px 10px;">{body}</p>'
            )
        }];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'pagingtoolbar',
            itemId: 'pager',
            dock: 'bottom',
            store: this.store,
            displayInfo: true,
            displayMsg: this.displayMessageText,
            emptyMsg: this.emptyMessageText
        }];
    },

    setFilter: function(values) {
        var pager = this.getDockedComponent('pager');

        this.getStore().getProxy().setExtraParam('filter', values ? Ext.encode(values) : null);
        this.getStore().load({
            params: {
                start: 0,
                page: 1
            }
        });
    },

    clearFilter: function() {
        this.setFilter(null);
    }
});
