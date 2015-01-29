Ext.define('Phlexible.task.portlet.MyTasks', {
    extend: 'Ext.dashboard.Panel',
    alias: 'widget.tasks-my-tasks-portlet',

    iconCls: Phlexible.Icon.get('clipboard-task'),
    bodyPadding: 5,

    imageUrl: '/bundles/phlexibletask/images/portlet-my-tasks.png',

    noActiveTasksText: '_noActiveTasksText',
    commentText: '_commentText',

    initComponent: function () {

        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.task.model.MyTask',
            id: 'id',
            //sorters: [{field: 'type', username: 'ASC'}],
            data: this.item.data
        });

        this.items = [
            {
                xtype: 'dataview',
                itemSelector: 'div.portlet-task',
                style: 'overflow: auto',
                singleSelect: true,
                emptyText: this.noActiveTasksText,
                deferEmptyText: false,
                autoHeight: true,
                store: this.store,
                tpl: new Ext.XTemplate(
                    '<tpl for=".">',
                    '<div id="portal_tasks_{id}" class="portlet-task" style="cursor: pointer; padding-bottom: 5px;">',
                    '<div>',
                    '<b><img src="{[Phlexible.bundleAsset("/tasks/icons/status_"+values.status+".png")]} width="16" height="16" style="vertical-align: middle;"> {[Phlexible.task.Strings.get(values.status)]}</b>, von<b> {create_user}</b>, <b>{create_date}</b>',
                    '</div>',
                    '<div style="padding-left: 20px;">',
                    '{text}',
                    '</div>',
                    '<tpl if="comment">',
                    '<div style="padding-left: 20px;">',
                    this.commentText + ': {comment}',
                    '</div>',
                    '</tpl>',
                    '</div>',
                    '</tpl>'
                ),
                listeners: {
                    dblclick: function (view, index) {
                        r = view.store.getAt(index);

                        if (!r) {
                            return;
                        }

                        Phlexible.Frame.loadPanel(
                            'Phlexible_task_MainPanel',
                            Phlexible.task.MainPanel,
                            {
                                id: r.get('id')
                            }
                        );
                    },
                    scope: this
                }
            }
        ];

        this.callParent(this);
    },

    updateData: function (data) {
        var taskMap = [];

        for (var i = 0; i < data.length; i++) {
            var row = data[i];
            taskMap.push(row.id);
            var r = this.store.getById(row.id);
            var update = false;
            if (r) {
                if (r.get('status') != row.status) {
                    update = true;
                    r.set('status', row.status);
                }
                if (r.get('text') != row.text) {
                    update = true;
                    r.set('text', row.text);
                }
            } else {
                update = true;
                this.store.add(new Phlexible.task.portlet.TaskRecord(row, row.id));

//                Phlexible.msg('Task', this.strings.new_task + ' "' + row.text + '".');
            }

            if (update) {
                Ext.fly('portal_tasks_' + row.id).frame('#8db2e3', 1);
            }
        }

        for (var i = this.store.getCount() - 1; i > 0; i--) {
            var r = this.store.getAt(i);
            if (taskMap.indexOf(r.id) == -1) {
//                Phlexible.msg('Task', this.strings.new_task + ' "' + r.get('text') + '".');
                this.store.remove(r);
            }
        }

        if (!this.store.getCount()) {
            this.store.removeAll();
        }

        this.store.sort('type', 'ASC');
    }
});
