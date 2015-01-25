Ext.define('Phlexible.message.view.filter.MainPanel', {
    extend: 'Ext.Panel',
    alias: 'widget.message-filter-main',

    iconCls: Phlexible.Icon.get(Phlexible.Icon.FILTER),
    layout: 'border',

    initComponent: function () {
        this.items = [
            {
                xtype: 'message-filter-list',
                itemId: 'list',
                region: 'west',
                width: 200,
                collapsible: true,
                split: true,
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
                        xtype: 'message-filter-criteria',
                        itemId: 'criteria',
                        region: 'west',
                        width: 550,
                        disabled: true,
                        ready: true,
                        split: true,
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
                        xtype: 'message-filter-preview',
                        itemId: 'preview',
                        region: 'center',
                        disabled: true
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
