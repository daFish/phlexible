Ext.define('Phlexible.message.view.filter.List', {
    extend: 'Ext.grid.Panel',
    requires: [
        'Phlexible.message.model.Filter'
    ],
    xtype: 'message.filter.list',

    componentCls: 'p-message-filter-list',
    loadMask: true,
    hideMode: 'offsets',
    emptyText: '_emptyText',
    viewConfig: {
        deferEmptyText: false
    },

    actionsText: '_actionsText',
    titleText: '_titleText',
    saveFilterText: '_saveText',
    addFilterText: '_addFilterText',
    addFilterDescriptionText: '_addFilterDescriptionText',
    deleteFilterText: '_deleteFilterText',
    deleteFilterDescriptionText: '_deleteFilterDescriptionText',

    initComponent: function () {
        this.initMyColumns();
        this.initMyDockedItems();

        this.callParent(arguments);
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
                        tooltip: this.deleteFilterText,
                        iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
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
                },
                '->',
                {
                    text: this.saveText,
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.SAVE),
                    handler: this.saveFilter,
                    scope: this
                }
            ]
        }];
    },

    /**
     * Save method to save modified data
     */
    saveFilter: function () {
        this.fireEvent('saveFilter', this.filter);
    },

    addFilter: function () {
        Ext.MessageBox.prompt(this.addFilterText, this.addFilterDescriptionText, function (btn, title) {
            if (btn !== 'ok') {
                return;
            }
            var filter = Ext.create('Phlexible.message.model.Filter', {
                title: title,
                userId: Phlexible.User.getId(),
                createdAt: new Date(),
                modifiedAt: new Date()
            });
            this.getStore().add(filter);

            this.fireEvent('addFilter', filter);
        }, this);
    },

    deleteFilter: function (grid, filter) {
        Ext.MessageBox.confirm(this.deleteFilterText, Ext.String.format(this.deleteFilterDescriptionText, filter.get('title')), function (btn) {
            if (btn !== 'yes') {
                return;
            }
            filter.drop();

            this.fireEvent('deleteFilter', filter);
        }, this);
    }
});
