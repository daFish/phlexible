Ext.define('Phlexible.task.view.List', {
    extend: 'Ext.grid.GridPanel',
    requires: [
        'Phlexible.task.model.Task'
    ],
    xtype: 'tasks.list',

    cls: 'p-tasks-list',
    deferEmptyText: false,
    loadMask: true,
    viewConfig: {
        deferEmptyText: false
    },

    emptyText: '_emptyText',
    idText: '_idText',
    typeText: '_typeText',
    componentText: '_componentText',
    statusText: '_statusText',
    titleText: '_titleText',
    taskText: '_taskText',
    descriptionText: '_descriptionText',
    assignedToText: '_assignedToText',
    createUserText: '_createUserText',
    createDateText: '_createDateText',
    reloadText: '_reloadText',

    initComponent: function () {
        this.initMyStore();
        this.initMyColumns();
        this.initMyDockedItems();
        this.initMyListeners();

        this.callParent(arguments);
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.task.model.Task',
            proxy: {
                type: 'ajax',
                url: Phlexible.Router.generate('phlexible_task_get_tasks'),
                simpleSortMode: true,
                remoteSort: true,
                reader: {
                    type: 'json',
                    rootProperty: 'tasks',
                    idProperty: 'id',
                    totalProperty: 'total'
                },
                extraParams: {
                    start: 0,
                    limit: 20
                }
            },
            listeners: {
                load: function () {
                    if (this.taskId) {
                        var row = this.getStore().find('id', this.taskId);
                        if (row !== -1) {
                            this.getSelectionModel().selectRow(row);
                        }
                        this.taskId = false;
                    }
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
                width: 220,
                hidden: true
            }, {
                header: this.typeText,
                dataIndex: 'type',
                width: 200,
                hidden: true
            }, {
                header: this.componentText,
                dataIndex: 'component',
                width: 100,
                hidden: true
            }, {
                header: this.statusText,
                dataIndex: 'status',
                width: 120,
                renderer: function (s) {
                    return Phlexible.inlineIcon('p-task-status_' + s + '-icon') + ' ' + Phlexible.task.Strings[s];
                }
            }, {
                header: this.titleText,
                dataIndex: 'title',
                width: 140
            }, {
                header: this.taskText,
                dataIndex: 'text',
                width: 150
            }, {
                header: this.descriptionText,
                dataIndex: 'description',
                width: 150,
                flex: 1
            }, {
                header: this.assignedToText,
                dataIndex: 'assigned_user',
                width: 100
            }, {
                header: this.createUserText,
                dataIndex: 'create_user',
                width: 100
            }, {
                header: this.createDateText,
                dataIndex: 'create_date',
                width: 120,
                hidden: true
            }];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'top',
            items: [{
                text: this.reloadText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.RELOAD),
                handler: function () {
                    this.store.reload();
                },
                scope: this
            }]
        },{
            xtype: 'pagingtoolbar',
            dock: 'bottom',
            store: this.store
        }];
    },

    initMyListeners: function() {
        this.on({
            selectionchange: function (sm) {
                var r = sm.getSelected();
                if (!r) {
                    return;
                }
                this.fireEvent('taskchange', r);
            },
            scope: this
        });
    },

    setStatus: function (task_id, new_status, comment) {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('tasks_create_status'),
            params: {
                task_id: task_id,
                new_status: new_status,
                comment: encodeURIComponent(comment)
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    Phlexible.success(data.msg);

                    this.store.reload();
                } else {
                    Ext.MessageBox.alert('Failure', data.msg);
                }
            },
            scope: this
        });
    }
});
