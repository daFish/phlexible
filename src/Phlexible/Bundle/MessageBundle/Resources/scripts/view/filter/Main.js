Ext.define('Phlexible.message.view.filter.Main', {
    extend: 'Ext.Panel',
    requires: [
        'Phlexible.message.view.filter.Criteria',
        'Phlexible.message.view.filter.List',
        'Phlexible.message.view.filter.Preview'
    ],
    xtype: 'message.filter.main',

    cls: 'p-message-filter-main',
    iconCls: Phlexible.Icon.get(Phlexible.Icon.FILTER),
    layout: 'border',
    border: false,

    initComponent: function () {
        this.items = [
            {
                xtype: 'message.filter.list',
                itemId: 'list',
                region: 'west',
                padding: '5 0 5 5',
                width: 200,
                collapsible: true,
                split: false,
                listeners: {
                    filterChange: function (record) {
                        if (this.getCriteriaPanel().ready === true) {
                            this.getCriteriaPanel().ready = false;
                            this.getCriteriaPanel().loadData(record);
                        }
                    },
                    filterDeleted: function (record) {
                        this.fireEvent('filterDeleted');

                        this.getCriteriaPanel().clear();
                        this.getCriteriaPanel().disable();
                        this.getPreviewPanel().clear();
                        this.getPreviewPanel().disable();
                    },
                    scope: this
                }
            },
            {
                xtype: 'panel',
                itemId: 'wrap',
                region: 'center',
                layout: 'border',
                border: false,
                items: [
                    {
                        xtype: 'message.filter.criteria',
                        itemId: 'criteria',
                        region: 'west',
                        padding: '5 0 5 5',
                        width: 550,
                        disabled: true,
                        ready: true,
                        split: false,
                        listeners: {
                            reload: function () {
                                this.getListPanel().getStore().reload();
                            },
                            refreshPreview: function (values) {
                                this.getPreviewPanel().setFilter(values);
                            },
                            scope: this
                        }
                    },
                    {
                        xtype: 'message.filter.preview',
                        itemId: 'preview',
                        region: 'center',
                        padding: '5 0 5 5',
                        autoLoad: false
                    }
                ]
            }
        ];

        this.callParent(arguments);
    },

    getListPanel: function () {
        return this.getComponent('list');
    },

    getCriteriaPanel: function () {
        return this.getComponent('wrap').getComponent('criteria');
    },

    getPreviewPanel: function () {
        return this.getComponent('wrap').getComponent('preview');
    },

    loadParams: function () {
        return;
    }
});
