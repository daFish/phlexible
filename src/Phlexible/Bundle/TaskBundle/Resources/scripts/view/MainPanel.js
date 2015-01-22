Ext.define('Phlexible.task.view.MainPanel', {
    extend: 'Ext.Panel',
    alias: 'widget.tasks-main',

    cls: 'p-tasks-main',
    iconCls: Phlexible.Icon.get('clipboard-task'),
    layout: 'border',
    border: false,

    params: {},

    commentsText: '_commentsText',
    transitionsText: '_transitionsText',
    statusText: '_statusText',
    commentText: '_commentText',
    assignToMeText: '_assignToMeText',
    assignText: '_assignText',

    loadParams: function (params) {
        if (params.id) {
            this.getComponent(1).getComponent(0).taskId = params.id;
            this.getComponent('filter').onReset();
            this.getComponent('filter').updateFilter();
        }
    },

    initComponent: function () {
        this.initMyItems();
        this.initMyTemplates();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'tasks-filter',
                itemdId: 'filter',
                region: 'west',
                width: 200,
                padding: '5 0 5 5',
                collapsible: true,
                listeners: {
                    updateFilter: function (values) {
                        this.getComponent(1).getComponent(0).getStore().baseParams = values;
                        this.getComponent(1).getComponent(0).getStore().reload();
                    },
                    scope: this
                }
            },
            {
                xtype: 'panel',
                region: 'center',
                layout: 'border',
                border: false,
                items: [{
                    xtype: 'tasks-list',
                    region: 'center',
                    padding: 5,
                    taskId: this.params.id || false,
                    listeners: {
                        taskchange: function(r) {
                            var taskView = this.getTaskView(),
                                statusMenu = taskView.getDockedItem('tbar').getComponent('statusBtn').getMenu();
                            this.viewTemplate.overwrite(taskView.body, r.data);
                            statusMenu.removeAll();
                            Ext.each(r.get('states'), function(state) {
                                statusMenu.add({
                                    text: state,
                                    iconCls: Phlexible.task.TransitionIcons[state]
                                });
                            });

                            this.commentsTemplate.overwrite(this.getCommentsView().body, r.data.comments);
                            this.transitionsTemplate.overwrite(this.getTransitionsView().body, r.data.transitions);
                        },
                        scope: this
                    }
                },{
                    region: 'east',
                    layout: 'border',
                    width: 400,
                    border: false,
                    items: [{
                        region: 'north',
                        height: 230,
                        padding: '5 5 0 0',
                        html: '&nbsp;',
                        dockedItems: [{
                            xtype: 'toolbar',
                            itemId: 'tbar',
                            dock: 'top',
                            items: [{
                                text: this.statusText,
                                itemId: 'statusBtn',
                                menu: []
                            },{
                                text: this.commentText,
                                iconCls: Phlexible.Icon.get('balloon'),
                                handler: function() {
                                    var w = Ext.create('Phlexible.task.window.CommentWindow');
                                    w.show();
                                },
                                scope: this
                            },{
                                text: this.assignToMeText,
                                handler: function() {
                                    var w = Ext.create('Phlexible.task.window.AssignWindow');
                                    w.show();
                                },
                                scope: this
                            },{
                                text: this.assignText,
                                handler: function() {
                                    var w = Ext.create('Phlexible.task.window.AssignWindow');
                                    w.show();
                                },
                                scope: this
                            }]
                        }]
                    },{
                        xtype: 'tabpanel',
                        region: 'center',
                        padding: '5 5 5 0',
                        activeTab: 0,
                        deferredRender: false,
                        items: [{
                            title: this.commentsText,
                            iconCls: Phlexible.Icon.get('balloon'),
                            html: '&nbsp;'
                        },{
                            title: this.transitionsText,
                            iconCls: Phlexible.Icon.get('arrow-switch'),
                            html: '&nbsp;'
                        }]
                    }]
                }]
            }
        ];
    },

    initMyTemplates: function() {
        this.viewTemplate = new Ext.XTemplate(
            '<div class="p-tasks-view">',
            '<table cellpadding="0" cellspacing="5">',
            '<colgroup>',
            '<col width="100" />',
            '<col width="240" />',
            '</colgroup>',
            '<tr>',
            '<th>{[Phlexible.task.Strings.task]}</th>',
            '<td>{title}</td>',
            '</tr>',
            '<tr>',
            '<th>{[Phlexible.task.Strings.task]}</th>',
            '<td>{text}</td>',
            '</tr>',
            '<tr>',
            '<th>{[Phlexible.task.Strings.status]}</th>',
            '<td>{[Phlexible.inlineIcon(\"p-task-status_\"+values.status+\"-icon\")]} {[Phlexible.task.Strings.get(values.status)]}</td>',
            '</tr>',
            '<tr>',
            '<th colspan="2">{[Phlexible.task.Strings.description]}</th>',
            '</tr>',
            '<tr>',
            '<td colspan="2">{description}</td>',
            '</tr>',
            '<tr>',
            '<th>{[Phlexible.task.Strings.assigned_to]}</th>',
            '<td>{assigned_user}</td>',
            '</tr>',
            '<tr>',
            '<th>{[Phlexible.task.Strings.create_user]}</th>',
            '<td>{create_user}</td>',
            '</tr>',
            '<tr>',
            '<th>{[Phlexible.task.Strings.create_date]}</th>',
            '<td>{create_date}</td>',
            '</tr>',
            '</table>',
            '</div>'
        );

        this.commentsTemplate = new Ext.XTemplate(
            '<div class="p-tasks-comments">',
            '<tpl for=".">',
            '<div class="p-tasks-comment">',
            '<div class="p-tasks-by">{create_user} kommentierte - {create_date}</div>',
            '<div class="p-tasks-text">{comment}</div>',
            '</div>',
            '</tpl>',
            '</div>'
        );

        this.transitionsTemplate = new Ext.XTemplate(
            '<div class="p-tasks-transitions">',
            '<tpl for=".">',
            '<div class="p-tasks-transition">',
            '<div class="p-tasks-by">{create_user} änderte - {create_date}</div>',
            '<div class="p-tasks-text">' +
            '<div style="float: left;">{[Phlexible.inlineIcon(\"p-task-status_\" + values.old_state + \"-icon\")]} {old_state}</div>' +
            '<div style="margin-left: 120px;">{[Phlexible.inlineIcon(\"p-task-goto-icon\")]} ' +
            '{[Phlexible.inlineIcon(\"p-task-status_\" + values.new_state + \"-icon\")]} {new_state}</div>' +
            '<div style="clear: left; "></div>' +
            '</div>',
            '</div>',
            '</tpl>',
            '</div>'
        );
    },

    getFilterPanel: function() {
        return this.getComponent(0);
    },

    getTaskGrid: function() {
        return this.getComponent(1).getComponent(0);
    },

    getTaskView: function() {
        return this.getComponent(1).getComponent(1).getComponent(0);
    },

    getCommentsView: function() {
        return this.getComponent(1).getComponent(1).getComponent(1).getComponent(0);
    },

    getTransitionsView: function() {
        return this.getComponent(1).getComponent(1).getComponent(1).getComponent(1);
    }
});
