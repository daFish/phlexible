Ext.define('Phlexible.message.view.filter.List', {
    extend: 'Ext.grid.GridPanel',
    xtype: 'message.filter.list',

    cls: 'p-message-filter-list',
    loadMask: true,
    hideMode: 'offsets',
    emptyText: '_emptyText',
    viewConfig: {
        deferEmptyText: false
    },

    actionsText: '_actionsText',
    titleText: '_titleText',
    addFilterText: '_addFilterText',
    addFilterDescriptionText: '_addFilterDescriptionText',
    deleteFilterText: '_deleteFilterText',
    deleteFilterDescriptionText: '_deleteFilterDescriptionText',

    initComponent: function () {
        this.initMyStore();
        this.initMyColumns();
        this.initMyDockedItems();
        this.initMyListeners();

        this.callParent(arguments);
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.message.model.Filter',
            proxy: {
                type: 'ajax',
                url: Phlexible.Router.generate('phlexible_message_get_filters'),
                simpleSortMode: true,
                reader: {
                    type: 'json',
                    idProperty: 'id'
                }
            },
            autoLoad: true
        });
    },

    initMyColumns: function() {
        this.columns = [
            {
                header: this.titleText,
                dataIndex: 'title',
                sortable: true,
                flex: 1
            },
            {
                xtype: 'actioncolumn',
                header: this.actionsText,
                width: 40,
                items: [
                    {
                        iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                        tooltip: this.deleteFilterText,
                        handler: this.deleteFilter,
                        scope: this
                    }
                ]
            }
        ];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'top',
            items: [
                {
                    text: this.addFilterText,
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                    handler: this.addFilter,
                    scope: this
                }
            ]
        }];
    },

    initMyListeners: function() {
        this.addListener({
            rowdblclick: this.filterChange,
            scope: this
        });
    },

    addFilter: function () {
        Ext.MessageBox.prompt(this.addFilterText, this.addFilterDescriptionText, function (btn, title) {
            if (btn === 'ok') {
                Ext.Ajax.request({
                    url: Phlexible.Router.generate('phlexible_message_post_filters'),
                    params: {
                        title: title
                    },
                    success: function (response) {
                        var result = Ext.decode(response.responseText);

                        if (result.success) {
                            Phlexible.Notify.success(result.msg);
                            this.store.reload();
                        } else {
                            Phlexible.Notify.failure(result.msg);
                        }
                    },
                    scope: this
                });
            }
        }, this);
    },

    filterChange: function (grid, record) {
        this.fireEvent('filterChange', record);
    },

    deleteFilter: function (grid, record) {
        Ext.MessageBox.confirm(this.deleteFilterText, Ext.String.format(this.deleteFilterDescriptionText, record.get('title')), function (btn) {
            if (btn != 'yes') {
                return;
            }
            Ext.Ajax.request({
                url: Phlexible.Router.generate('phlexible_message_delete_filter', {filterId: record.get('id')}),
                success: function (response) {
                    var data = Ext.decode(response.responseText);

                    if (data.success) {
                        Phlexible.Notify.success(data.msg);
                        this.store.reload();
                        this.fireEvent('filterDeleted');
                    } else {
                        Phlexible.Notify.failure(data.msg);
                    }
                },
                scope: this
            });
        }, this);
    }
});
