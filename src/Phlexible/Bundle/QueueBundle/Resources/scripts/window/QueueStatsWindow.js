Ext.define('Phlexible.queue.window.QueueStatsWindow', {
    extend: 'Ext.window.Window',

    title: Phlexible.queue.Strings.window.QueueStatsWindow.queue,
    width: 900,
    height: 600,
    iconCls: Phlexible.Icon.get('application-task'),
    layout: 'fit',
    constrainHeader: true,
    maximizable: true,
    modal: true,

    noJobsText: Phlexible.queue.Strings.window.QueueStatsWindow.noJobsText,
    idText: Phlexible.queue.Strings.window.QueueStatsWindow.idText,
    commandText: Phlexible.queue.Strings.window.QueueStatsWindow.commandText,
    priorityText: Phlexible.queue.Strings.window.QueueStatsWindow.priorityText,
    statusText: Phlexible.queue.Strings.window.QueueStatsWindow.statusText,
    createdAtText: Phlexible.queue.Strings.window.QueueStatsWindow.createdAtText,
    startedAtText: Phlexible.queue.Strings.window.QueueStatsWindow.startedAtText,
    finishedAtText: Phlexible.queue.Strings.window.QueueStatsWindow.finishedAtText,
    reloadText: Phlexible.queue.Strings.window.QueueStatsWindow.reloadText,

    initComponent: function () {
        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = {
            xtype: 'grid',
            border: false,
            emptyText: this.noJobsText,
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
                    header: this.idText,
                    dataIndex: 'id',
                    width: 250,
                    hidden: true
                }, {
                    header: this.commandText,
                    dataIndex: 'command',
                    width: 250
                }, {
                    header: this.priorityText,
                    dataIndex: 'priority',
                    width: 50,
                    flex: 1
                }, {
                    header: this.statusText,
                    dataIndex: 'status',
                    width: 60
                }, {
                    header: this.createdAtText,
                    dataIndex: 'create_time',
                    width: 120
                }, {
                    header: this.startedAtText,
                    dataIndex: 'start_time',
                    width: 120
                }, {
                    header: this.finishedAtText,
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
                text: this.reloadText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.RELOAD),
                handler: function () {
                    this.getComponent(0).store.reload();
                },
                scope: this
            }]
        }];
    }
});
