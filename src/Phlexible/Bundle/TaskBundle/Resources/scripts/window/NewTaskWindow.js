Ext.define('Phlexible.tasks.NewTaskWindow', {
    extend: 'Ext.window.Window',

    title: Phlexible.tasks.Strings.new_task,
    strings: Phlexible.tasks.Strings,
    iconCls: 'p-task-component-icon',
    width: 400,
    minWidth: 400,
    height: 270,
    minHeight: 270,
    layout: 'fit',
    modal: true,

    payload: {},
    component_filter: null,

    initComponent: function () {
        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'form',
                border: false,
                bodyStyle: 'padding: 5px',
                monitorValid: true,
                items: [
                    {
                        xtype: 'combo',
                        fieldLabel: this.strings.task,
                        hiddenName: 'task',
                        anchor: '100%',
                        allowBlank: false,
                        emptyText: this.strings.please_choose,
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'task'],
                            proxy: {
                                type: 'ajax',
                                url: Phlexible.Router.generate('tasks_types'),
                                simpleSortMode: true,
                                reader: {
                                    type: 'json',
                                    rootProperty: 'tasks',
                                    idProperty: 'id'
                                },
                                extraParams: {
                                    component: this.component_filter
                                }
                            }
                        }),
                        editable: false,
                        displayField: 'task',
                        valueField: 'id',
                        mode: 'remote',
                        triggerAction: 'all',
                        selectOnFocus: true,
                        listeners: {
                            select: function (combo, r) {
                                if (r) {
                                    var c = this.getComponent(0).getComponent(1);
                                    c.lastQuery = null;
                                    c.store.baseParams.task_class = r.data.id;
                                    c.setValue('');
                                    c.enable();
                                }
                            },
                            scope: this
                        }
                    },
                    {
                        xtype: 'combo',
                        fieldLabel: this.strings.recipient,
                        hiddenName: 'recipient',
                        anchor: '100%',
                        allowBlank: false,
                        disabled: true,
                        emptyText: this.strings.please_choose,
                        store: Ext.create('Ext.data.Store', {
                            fields: ['uid', 'username'],
                            proxy: {
                                type: 'ajax',
                                url: Phlexible.Router.generate('tasks_recipients'),
                                reader: {
                                    type: 'json',
                                    rootProperty: 'users',
                                    idProperty: 'uid'
                                },
                                extraParams: {
                                    task_class: false
                                }
                            }
                        }),
                        editable: false,
                        displayField: 'username',
                        valueField: 'uid',
                        mode: 'remote',
                        triggerAction: 'all',
                        selectOnFocus: true
                    },
                    {
                        xtype: 'textarea',
                        anchor: '100%',
                        height: 140,
                        allowBlank: false,
                        fieldLabel: this.strings.comment,
                        name: 'comment'
                    }
                ],
                bindHandler: function () {
                    var valid = true;
                    this.form.items.each(function (f) {
                        if (!f.isValid(true)) {
                            valid = false;
                            return false;
                        }
                    });
                    if (this.ownerCt.buttons) {
                        for (var i = 0, len = this.ownerCt.buttons.length; i < len; i++) {
                            var btn = this.ownerCt.buttons[i];
                            if (btn.formBind === true && btn.disabled === valid) {
                                btn.setDisabled(!valid);
                            }
                        }
                    }
                    this.fireEvent('clientvalidation', this, valid);
                }
            }
        ];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'bottom',
            ui: 'footer',
            items: [
                {
                    text: this.strings.cancel,
                    handler: this.close,
                    scope: this
                },
                {
                    text: this.strings.send,
                    handler: this.onSend,
                    formBind: true,
                    scope: this
                }
            ]
        }];
    },

    onSend: function () {
        if (!this.getComponent(0).form.isValid()) {
            return;
        }

        this.getComponent(0).form.submit({
            url: Phlexible.Router.generate('tasks_create_task'),
            params: {
                payload: Ext.encode(this.payload)
            },
            failure: function (form, action) {
                var result = action.result;

                Ext.MessageBox.alert('Failure', result.msg);
            },
            success: function (form, action) {
                var result = action.result;

                if (result.success) {
                    Phlexible.success(result.msg);
                    this.fireEvent('create', this);
                    this.close();
                }
                else {
                    Ext.MessageBox.alert('Failure', result.msg);
                }
            },
            scope: this
        });
    }
});
