Ext.define('Phlexible.queue.QueueStatsWindow', {
    extend: 'Ext.window.Window',

    title: Phlexible.queue.Strings.queue,
    strings: Phlexible.queue.Strings,
    width: 900,
    height: 600,
    iconCls: Phlexible.Icon.get('application-task'),
    layout: 'fit',
    constrainHeader: true,
    maximizable: true,
    modal: true,

    initComponent: function () {
        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = {
            xtype: 'grid',
            border: false,
            emptyText: this.strings.no_jobs,
            store: Ext.create('Ext.data.Store', {
                model: 'Phlexible.queue.model.Job',
                proxy: {
                    type: 'ajax',
                    url: Phlexible.Router.generate('queue_list'),
                    simpleSortMode: true,
                    reader: {
                        type: 'json',
                        rootProperty: 'data',
                        idProperty: 'id'
                    },
                    extraParams: this.storeExtraParams
                },
                autoLoad: true
            }),
            columns: [
                {
                    header: this.strings.id,
                    dataIndex: 'id',
                    width: 250,
                    hidden: true
                }, {
                    header: this.strings.command,
                    dataIndex: 'command',
                    width: 250
                }, {
                    header: this.strings.priority,
                    dataIndex: 'priority',
                    width: 50,
                    flex: 1
                }, {
                    header: this.strings.status,
                    dataIndex: 'status',
                    width: 60
                }, {
                    header: this.strings.create_time,
                    dataIndex: 'create_time',
                    width: 120
                }, {
                    header: this.strings.start_time,
                    dataIndex: 'start_time',
                    width: 120
                }, {
                    header: this.strings.end_time,
                    dataIndex: 'end_time',
                    width: 120
                }
            ],
            plugins: [{
                ptype: 'rowexpander',
                rowBodyTpl: [
                    '<p>{output}</p>'
                ]
            }]
        };
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'top',
            items: [{
                text: this.strings.reload,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.RELOAD),
                handler: function () {
                    this.getComponent(0).store.reload();
                },
                scope: this
            }]
        }];
    }
});
