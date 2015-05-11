Ext.define('Phlexible.queue.view.Main', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Phlexible.queue.model.Job'
    ],
    xtype: 'queue.main',

    cls: 'p-queue-main',

    iconCls: Phlexible.Icon.get('application-task'),
    layout: 'border',
    border: false,
    referenceHolder: true,
    viewModel: {
        stores: {
            jobs: {
                model: 'Phlexible.queue.model.Job',
                autoLoad: true
            }
        }
    },

    queueText: '_queueText',
    noJobsText: '_noJobsText',
    idText: '_idText',
    commandText: '_commandText',
    priorityText: '_priorityText',
    stateText: '_statusText',
    createdAtText: '_createdAtText',
    executeAfterText: '_executeAfter',
    startedAtText: '_startedAtText',
    finishedAtText: '_finishedAtText',
    reloadText: '_reloadText',
    exitCodeText: '_exitCodeText',
    runTimeText: '_runTimeText',
    memoryUsageText: '_memoryUsageText',
    maxRuntimeText: '_maxRuntimeText',
    outputText: '_outputText',
    errorOutputText: '_errorOutputText',
    stackTraceText: '_stackTraceText',

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [{
            xtype: 'grid',
            region: 'center',
            title: this.queueText,
            padding: 5,
            emptyText: this.noJobsText,
            reference: 'list',
            bind: {store: '{jobs}'},
            columns: [
                {
                    header: this.idText,
                    dataIndex: 'id',
                    width: 250,
                    hidden: true
                }, {
                    header: this.commandText,
                    dataIndex: 'fullCommand',
                    width: 250,
                    flex: 1
                }, {
                    header: this.priorityText,
                    dataIndex: 'priority',
                    width: 50
                }, {
                    header: this.stateText,
                    dataIndex: 'state',
                    width: 60
                }, {
                    xtype: 'datecolumn',
                    header: this.createdAtText,
                    dataIndex: 'createdAt',
                    width: 120,
                    format: 'Y-m-d H:i:s'
                }, {
                    xtype: 'datecolumn',
                    header: this.executeAfterText,
                    dataIndex: 'executeAfter',
                    width: 120,
                    format: 'Y-m-d H:i:s',
                    hidden: true
                }, {
                    xtype: 'datecolumn',
                    header: this.startedAtText,
                    dataIndex: 'startedAt',
                    width: 120,
                    format: 'Y-m-d H:i:s'
                }, {
                    xtype: 'datecolumn',
                    header: this.finishedAtText,
                    dataIndex: 'finishedAt',
                    width: 120,
                    format: 'Y-m-d H:i:s'
                }, {
                    header: this.exitCodeText,
                    dataIndex: 'exitCode',
                    width: 50,
                    hidden: true
                }, {
                    header: this.runTimeText,
                    dataIndex: 'runTime',
                    width: 50
                }, {
                    header: this.memoryUsageText,
                    dataIndex: 'memoryUsage',
                    width: 50,
                    hidden: true
                }
            ],
            dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                items: [{
                    text: this.reloadText,
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.RELOAD),
                    handler: function () {
                        this.getViewModel().getStore('jobs').reload();
                    },
                    scope: this
                }]
            }]
        },{
            xtype: 'form',
            region: 'south',
            height: 245,
            padding: '0 5 5 5',
            bodyPadding: 5,
            items: [{
                xtype: 'textfield',
                fieldLabel: this.commandText,
                anchor: '100%',
                readOnly: true,
                bind: {
                    value: '{list.selection.fullCommand}'
                }
            }, {
                layout: 'hbox',
                border: false,
                defaults: {
                    flex: 1,
                    layout: 'anchor',
                    border: false
                },
                items: [{
                    defaults: {
                        anchor: '-10',
                        readOnly: true
                    },
                    items: [{
                        xtype: 'displayfield',
                        cls: 'p-badge',
                        fieldLabel: this.stateText,
                        bind: {
                            value: '{list.selection.state}'
                        },
                        listeners: {
                            change: function(field, value, oldValue) {
                                field.removeCls('p-badge-' + oldValue);
                                field.addCls('p-badge-' + value);
                            }
                        }
                    },{
                        xtype: 'textfield',
                        fieldLabel: this.runTimeText,
                        bind: {
                            value: '{list.selection.runTime}'
                        }
                    },{
                        xtype: 'textfield',
                        fieldLabel: this.memoryUsageText,
                        bind: {
                            value: '{list.selection.memoryUsageFormatted}'
                        }
                    }]
                },{
                    defaults: {
                        anchor: '-10',
                        readOnly: true
                    },
                    items: [{
                        xtype: 'textfield',
                        fieldLabel: this.priorityText,
                        bind: {
                            value: '{list.selection.priority}'
                        }
                    },{
                        xtype: 'textfield',
                        fieldLabel: this.maxRuntimeText,
                        bind: {
                            value: '{list.selection.maxRuntime}'
                        }
                    },{
                        xtype: 'textfield',
                        fieldLabel: this.executeAfterText,
                        bind: {
                            value: '{list.selection.executeAfterFormatted}'
                        }
                    }]
                },{
                    defaults: {
                        anchor: '-0',
                        readOnly: true
                    },
                    items: [{
                        xtype: 'textfield',
                        fieldLabel: this.createdAtText,
                        bind: {
                            value: '{list.selection.createdAtFormatted}'
                        }
                    }, {
                        xtype: 'textfield',
                        fieldLabel: this.startedAtText,
                        bind: {
                            value: '{list.selection.startedAtFormatted}'
                        }
                    },{
                        xtype: 'textfield',
                        fieldLabel: this.finishedAtText,
                        bind: {
                            value: '{list.selection.finishedAtFormatted}'
                        }
                    }]
                }]
            },{
                layout: 'hbox',
                border: false,
                defaults: {
                    flex: 1,
                    layout: 'anchor',
                    border: false
                },
                items: [{
                    defaults: {
                        anchor: '-10',
                        height: 120,
                        readOnly: true
                    },
                    items: [{
                        xtype: 'textarea',
                        fieldLabel: this.outputText,
                        readOnly: true,
                        bind: {
                            value: '{list.selection.output}'
                        }
                    }]
                },{
                    defaults: {
                        anchor: '-0',
                        height: 120,
                        readOnly: true
                    },
                    items: [{
                        xtype: 'textarea',
                        fieldLabel: this.errorOutputText,
                        readOnly: true,
                        bind: {
                            value: '{list.selection.errorOutput}'
                        }
                    }]
                }]
            }]
        }];
    }
});
