Ext.define('Phlexible.task.view.FilterPanel', {
    extend: 'Ext.form.FormPanel',
    alias: 'widget.tasks-filter',

    title: '_FilterPanel',
    cls: 'p-tasks-filter',
    iconCls: Phlexible.Icon.get('funnel'),
    bodyPadding: 5,
    autoScroll: true,

    commentsText: '_commentsText',
    tasksText: '_tasksText',
    assignedToMeText: '_assignedToMeText',
    createdByMeText: '_createdByMeText',
    involvedText: '_involvedText',
    allTasksText: '_allTasksText',
    statusText: '_statusText',
    openText: '_openText',
    rejectedText: '_rejectedText',
    reopenedText: '_reopenedText',
    finishedText: '_finishedText',
    closedText: '_closedText',

    initComponent: function () {
        this.initMyTasks();
        this.initMyItems();
        this.initMyDockedItems();
        this.initMyListeners();
        this.loadFilterValues();

        this.callParent(arguments);
    },

    initMyTasks: function() {
        this.task = new Ext.util.DelayedTask(this.updateFilter, this);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'panel',
                title: this.commentsText,
                layout: 'form',
                margin: '0 0 5 0',
                frame: true,
                collapsible: true,
                labelAlign: 'top',
                hidden: true,
                items: [
                    {
                        xtype: 'textfield',
                        hideLabel: true,
                        anchor: '-25',
                        name: 'comments',
                        labelAlign: 'top',
                        enableKeyEvents: true,
                        listeners: {
                            keyup: function (field, event) {
                                if (event.getKey() == event.ENTER) {
                                    this.task.cancel();
                                    this.updateFilter();
                                    return;
                                }

                                this.task.delay(500);
                            },
                            scope: this
                        }
                    }
                ]
            },
            {
                xtype: 'panel',
                title: this.tasksText,
                layout: 'form',
                margin: '0 0 5 0',
                frame: true,
                collapsible: true,
                defaults: {
                    hideLabel: true
                },
                items: [
                    {
                        xtype: 'radio',
                        name: 'tasks',
                        boxLabel: this.assignedToMeText,
                        inputValue: 'todos',
                        listeners: {
                            check: function (cb, checked) {
                                if (checked) {
                                    this.updateFilter();
                                }
                            },
                            scope: this
                        }
                    },
                    {
                        xtype: 'radio',
                        name: 'tasks',
                        boxLabel: this.createdByMeText,
                        inputValue: 'tasks',
                        listeners: {
                            check: function (cb, checked) {
                                if (checked) {
                                    this.updateFilter();
                                }
                            },
                            scope: this
                        }
                    },
                    {
                        xtype: 'radio',
                        name: 'tasks',
                        boxLabel: this.involvedText,
                        inputValue: 'involved',
                        checked: true,
                        listeners: {
                            check: function (cb, checked) {
                                if (checked) {
                                    this.updateFilter();
                                }
                            },
                            scope: this
                        }
                    },
                    {
                        xtype: 'radio',
                        name: 'tasks',
                        boxLabel: this.allTasksText,
                        inputValue: 'all',
                        listeners: {
                            check: function (cb, checked) {
                                if (checked) {
                                    this.updateFilter();
                                }
                            },
                            scope: this
                        }
                    }
                ]
            },
            {
                xtype: 'panel',
                title: this.statusText,
                layout: 'form',
                margin: '0 0 5 0',
                frame: true,
                collapsible: true,
                defaults: {
                    hideLabel: true
                },
                items: [
                    {
                        xtype: 'checkbox',
                        name: 'status_open',
                        boxLabel: Phlexible.Icon.inline(Phlexible.task.StatusIcons.open) + ' ' + this.openText,
                        checked: true,
                        listeners: {
                            check: this.updateFilter,
                            scope: this
                        }
                    },
                    {
                        xtype: 'checkbox',
                        name: 'status_rejected',
                        boxLabel: Phlexible.Icon.inline(Phlexible.task.StatusIcons.rejected) + ' ' + this.rejectedText,
                        checked: true,
                        listeners: {
                            check: this.updateFilter,
                            scope: this
                        }
                    },
                    {
                        xtype: 'checkbox',
                        name: 'status_reopened',
                        boxLabel: Phlexible.Icon.inline(Phlexible.task.StatusIcons.reopened) + ' ' + this.reopenedText,
                        checked: true,
                        listeners: {
                            check: this.updateFilter,
                            scope: this
                        }
                    },
                    {
                        xtype: 'checkbox',
                        name: 'status_finished',
                        boxLabel: Phlexible.Icon.inline(Phlexible.task.StatusIcons.finished) + ' ' + this.finishedText,
                        checked: true,
                        listeners: {
                            check: this.updateFilter,
                            scope: this
                        }
                    },
                    {
                        xtype: 'checkbox',
                        name: 'status_closed',
                        boxLabel: Phlexible.Icon.inline(Phlexible.task.StatusIcons.closed) + ' ' + this.closedText,
                        listeners: {
                            check: this.updateFilter,
                            scope: this
                        }
                    }
                ]
            }
        ];
    },

    initMyDockedItems: function() {
        /*
         this.tbar = ['->',{
         text: this.strings.reset,
         iconCls: 'p-task-reset-icon',
         handler: this.onReset,
         scope: this
         }];
         */
    },

    initMyListeners: function() {
        this.on('render', function () {
            var values = {
                tasks: 'involved',
                status_open: 1,
                status_rejected: 1,
                status_reopened: 1,
                status_finished: 1
            };

            this.fireEvent('updateFilter', values);
        }, this);
    },

    loadFilterValues: function() {
//        Ext.Ajax.request({
//            url: Phlexible.Router.generate('tasks_filtervalues'),
//            success: this.onLoadFilterValues,
//            scope: this
//        });
    },

    onReset: function () {
        this.form.reset();
    },

    onLoadFilterValues: function (response) {
        var data = Ext.decode(response.responseText);

//        if(data.priorities && data.priorities.length && Ext.isArray(data.priorities)) {
//            Ext.each(data.priorities, function(item) {
//                this.getComponent(1).add({
//                    xtype: 'checkbox',
//                    name: 'priority_' + item.id,
//                    boxLabel: '<img src="' + Phlexible.bundleAsset(/resources/asset/icon/messages/priority_' + item.title + '.png)" style="vertical-align: middle;" width="16" height="16" /> ' + item.title,
//                    listeners: {
//                        check: this.updateFilter,
//                        scope: this
//                    }
//                });
//            }, this);
//            this.getComponent(1).items.each(function(item) {
//                this.form.add(item);
//            }, this);
//        }

        this.doLayout();
    },

    updateFilter: function () {
        var values = this.form.getValues();

        this.fireEvent('updateFilter', values);
    }
});
