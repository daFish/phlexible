Ext.define('Phlexible.users.security.LoginWindow', {
    extend: 'Ext.Window',

    modal: false,
    closable: false,
    collapsible: false,
    draggable: false,
    resizable: false,
    border: true,
    shadow: true,
    width: 420,
    height: 340,
    layout: 'border',
    cls: 'p-security-login-window',

    initComponent: function () {
        this.items = [
            {
                region: 'north',
                frame: false,
                border: false,
                style: 'text-align: center;',
                html: '<img src="' + Phlexible.bundlePath + '/phlexiblegui/images/logo.gif" width="300" height="120" style="padding-top: 15px" />'
            },
            {
                region: 'center',
                xtype: 'form',
                bodyPadding: 10,
                frame: false,
                border: false,
                monitorValid: true,
                standardSubmit: true,
                url: this.checkPath,
                items: [
                    {
                        frame: false,
                        border: false,
                        html: this.enterUsernamePasswordText
                    },
                    {
                        frame: false,
                        border: false,
                        bodyStyle: 'padding-bottom: 10px; text-align: center; color: red;',
                        html: this.errorMessage || ''
                    },
                    {
                        xtype: 'hidden',
                        name: '_csrf_token',
                        value: this.csrfToken
                    },
                    {
                        xtype: 'textfield',
                        flex: 1,
                        fieldLabel: this.usernameText,
                        labelSeparator: "",
                        name: '_username',
                        msgTarget: 'under',
                        allowBlank: false,
                        value: this.lastUsername || ''
                    },
                    {
                        xtype: 'textfield',
                        inputType: 'password',
                        flex: 1,
                        fieldLabel: this.passwordText,
                        labelSeparator: "",
                        name: '_password',
                        msgTarget: 'under',
                        allowBlank: false
                    },
                    {
                        xtype: 'checkbox',
                        flex: 1,
                        labelSeparator: '',
                        fieldLabel: '&nbsp;',
                        boxLabel: this.rememberMeText,
                        name: '_remember_me',
                        msgTarget: 'under',
                        allowBlank: false
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

        this.buttons = [
            {
                text: this.lostPasswordText,
                handler: function() {
                    document.location.href = this.resetUrl
                },
                scope: this
            },
            {
                text: this.submitText,
                formBind: true,
                iconCls: 'p-user-login-icon',
                handler: this.submit,
                scope: this
            }
        ];

        this.on({
            render: function (c) {
                var keyMap = this.getKeyMap();
                keyMap.addBinding({
                    key: Ext.EventObjectImpl.ENTER,
                    fn: this.submit,
                    scope: this
                });
            },
            show: this.focusField,
            move: this.focusField,
            resize: this.focusField,
            scope: this
        });

        this.callParent(arguments);
    },

    focusField: function () {
        var c;
        if (this.getUsernameField().getValue()) {
            c = this.getPasswordField();
        } else {
            c = this.getUsernameField();
        }
        c.focus(false, 10);
    },

    getUsernameField: function () {
        return this.getComponent(1).getComponent(3);
    },

    getPasswordField: function () {
        return this.getComponent(1).getComponent(4);
    },

    submit: function () {
        var form = this.getComponent(1).getForm();
        if (!form.isValid()) return;
        form.submit();
    }
});
