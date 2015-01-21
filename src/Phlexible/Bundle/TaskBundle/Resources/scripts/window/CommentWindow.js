Ext.define('Phlexible.tasks.window.CommentWindow', {
    extend: 'Ext.window.Window',

    title: Phlexible.tasks.Strings.comment,
    width: 400,
    minWidth: 400,
    height: 270,
    minHeight: 270,
    layout: 'fit',
    modal: true,

    payload: {},
    componentFilter: null,

    cancelText: Phlexible.tasks.Strings.window.CommentWindow.cancelText,
    commentText: Phlexible.tasks.Strings.window.CommentWindow.commentText,

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
                        xtype: 'textarea',
                        anchor: '100%',
                        height: 140,
                        allowBlank: false,
                        hideLabel: true,
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
                    text: this.commentText,
                    handler: this.comment,
                    formBind: true,
                    scope: this
                }
            ]
        }];
    },

    comment: function () {
        if (!this.getComponent(0).form.isValid()) {
            return;
        }

        var values = this.getComponent(0).getForm().getValues();
        Phlexible.tasks.TaskManager.comment(this.taskId, values.comment, function(success, result) {
            if (success && result.success) {
                this.fireEvent('success');
                this.close();
            }
        });
    }
});
