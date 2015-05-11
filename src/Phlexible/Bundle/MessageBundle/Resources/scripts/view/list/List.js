Ext.define('Phlexible.message.view.list.List', {
    extend: 'Ext.grid.GridPanel',
    xtype: 'message.list.list',

    cls: 'p-message-list-list',
    loadMask: true,
    emptyText: '_emptyText',
    viewConfig: {
        deferEmptyText: false
    },

    displayMessageText: '_displayMessageText',
    emptyMessageText: '_emptyMessageText',
    idText: '_idText',
    subjectText: '_subjectText',
    typeText: '_typeText',
    channelText: '_channelText',
    roleText: '_roleText',
    userText: '_userText',
    createdAtText: '_createdAtText',
    typeInfoText: '_typeInfoText',
    typeErrorText: '_typeErrorText',

    initComponent: function () {
        this.autoLoad = this.autoLoad !== false;

        this.initMyColumns();
        this.initMyDockedItems();

        this.callParent(arguments);
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
                header: this.typeText,
                dataIndex: 'type',
                sortable: true,
                width: 70,
                renderer: function (v) {
                    return v !== undefined && v !== null ? Phlexible.Icon.inlineText(Phlexible.message.TypeIcons[v], Phlexible.Config.get('message.types')[v]) : '';
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
                width: 120
            }, {
                xtype: 'datecolumn',
                header: this.createdAtText,
                dataIndex: 'createdAt',
                format: 'Y-m-d H:i:s',
                sortable: true,
                width: 120
            }
        ];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'pagingtoolbar',
            itemId: 'pager',
            dock: 'bottom',
            //store: this.store,
            bind: {
                store: '{messages}'
            },
            displayInfo: true,
            displayMsg: this.displayMessageText,
            emptyMsg: this.emptyMessageText
        }];
    }
});
