Ext.define('Phlexible.task.window.AssignWindow', {
    extend: 'Ext.window.Window',

    title: '_AssignWindow',
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

    recipientText: '_recipientText',
    recipientEmptyText: '_recipientEmptyText',
    commentText: '_commentText',
    cancelText: '_cancelText',
    assignText: '_assignText',

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
                        fieldLabel: this.recipientText,
                        hiddenName: 'recipient',
                        anchor: '100%',
                        allowBlank: false,
                        emptyText: this.recipientEmptyText,
                        store: Ext.create('Ext.data.JsonStore', {
                            fields: ['uid', 'username'],
                            proxy: {
                                type: 'ajax',
                                url: Phlexible.Router.generate('tasks_recipients'),
                                simpleSortMode: true,
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
                        fieldLabel: this.commentText,
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
                    text: this.cancelText,
                    handler: this.close,
                    scope: this
                },
                {
                    text: this.assignText,
                    handler: this.assign,
                    formBind: true,
                    scope: this
                }
            ]
        }];
    },

    assign: function () {
        if (!this.getComponent(0).form.isValid()) {
            return;
        }

        var values = this.getComponent(0).getForm().getValues();
        Phlexible.task.TaskManager.assign(this.taskId, values.recipient, values.comment, function(success, result) {
            if (success && result.success) {
                this.fireEvent('success');
                this.close();
            }
        });
    }
});
