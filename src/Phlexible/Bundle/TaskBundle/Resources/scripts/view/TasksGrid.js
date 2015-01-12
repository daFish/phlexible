Ext.define('Phlexible.tasks.TasksGrid', {
    extend: 'Ext.grid.GridPanel',
    alias: 'widget.tasks-list',

    strings: Phlexible.tasks.Strings,
    cls: 'p-tasks-task-grid',
    viewConfig: {
        deferEmptyText: false,
        emptyText: Phlexible.tasks.Strings.no_tasks_found
    },
    loadMask: true,

    initComponent: function () {
        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.tasks.model.Task',
            proxy: {
                type: 'ajax',
                url: Phlexible.Router.generate('tasks_list'),
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

        this.columns = [
            {
                header: this.strings.id,
                dataIndex: 'id',
                width: 220,
                hidden: true
            }, {
                header: this.strings.type,
                dataIndex: 'type',
                width: 200,
                hidden: true
            }, {
                header: this.strings.component,
                dataIndex: 'component',
                width: 100,
                hidden: true
            }, {
                header: this.strings.status,
                dataIndex: 'status',
                width: 120,
                renderer: function (s) {
                    return Phlexible.inlineIcon('p-task-status_' + s + '-icon') + ' ' + Phlexible.tasks.Strings[s];
                }
            }, {
                header: this.strings.title,
                dataIndex: 'title',
                width: 140
            }, {
                header: this.strings.task,
                dataIndex: 'text',
                width: 150
            }, {
                header: this.strings.description,
                dataIndex: 'description',
                width: 150,
                flex: 1
            }, {
                header: this.strings.assigned_to,
                dataIndex: 'assigned_user',
                width: 100
            }, {
                header: this.strings.create_user,
                dataIndex: 'create_user',
                width: 100
            }, {
                header: this.strings.create_date,
                dataIndex: 'create_date',
                width: 120,
                hidden: true
            }];

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

        this.tbar = [{
            text: this.strings.reload,
            iconCls: 'p-task-reset-icon',
            handler: function () {
                this.store.reload();
            },
            scope: this
        }];

        this.bbar = new Ext.PagingToolbar({
            store: this.store
        });

        this.callParent(arguments);
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
