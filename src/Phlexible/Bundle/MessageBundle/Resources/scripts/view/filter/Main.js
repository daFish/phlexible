Ext.define('Phlexible.message.view.filter.Main', {
    extend: 'Ext.Panel',
    requires: [
        'Phlexible.message.view.filter.Criteria',
        'Phlexible.message.view.filter.List',
        'Phlexible.message.view.list.List'
    ],
    xtype: 'message.filter.main',

    componentCls: 'p-message-filter-main',
    iconCls: Phlexible.Icon.get(Phlexible.Icon.FILTER),
    layout: 'border',
    border: false,
    referenceHolder: true,
    viewModel: {
        stores: {
            filters: {
                model: 'Filter',
                autoLoad: true,
                sorters: [{
                    property: 'title',
                    direction: 'ASC'
                }]
            },
            messages: {
                model: 'Message',
                autoLoad: false,
                remoteSort: true,
                pageSize: 25,
                sorters: [{
                    property: 'createdAt',
                    direction: 'DESC'
                }]
            }
        }
    },

    initComponent: function () {
        this.items = [
            {
                xtype: 'message.filter.list',
                itemId: 'list',
                reference: 'list',
                region: 'west',
                padding: '5 0 5 5',
                width: 200,
                collapsible: true,
                split: false,
                bind: {
                    store: '{filters}'
                },
                listeners: {
                    saveFilter: function() {
                        this.getViewModel().getStore('filters').sync();
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
                        bind: {
                            title: '{list.selection.title}',
                            filter: '{list.selection}'
                        },
                        listeners: {
                            refreshPreview: function(expression) {
                                this.getPreviewPanel().setExpression(expression);
                            },
                            scope: this
                        }
                    },
                    {
                        xtype: 'message.list.list',
                        itemId: 'preview',
                        region: 'center',
                        padding: '5 0 5 5',
                        autoLoad: false,
                        bind: {
                            store: '{messages}',
                            expression: '{list.section.expression}'
                        }
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
