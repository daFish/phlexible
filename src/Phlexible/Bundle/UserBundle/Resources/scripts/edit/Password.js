/**
 * User password edit panel
 */
Ext.define('Phlexible.user.edit.Password', {
    extend: 'Ext.form.FormPanel',
    requires: [
        'Ext.ux.form.field.PasswordMeter'
    ],
    xtype: 'user.edit-password',

    iconCls: Phlexible.Icon.get('star'),
    bodyPadding: 10,
    defaultType: 'textfield',
    fieldDefaults:{
        labelWidth: 130,
        labelAlign: 'top',
        msgTarget: 'under'
    },

    key: 'password',

    passwordText: '_password',
    passwordRepeatText: '_password_repeat',
    passwordsDontMatchText: '_passwords_dont_match',
    notifyUserText: 'notify_user',
    generatePasswordText: '_generate_password_text',
    generatedPasswordText: '_generated_password',
    generateText: '_generate',
    addOptinText: '_addOptin',
    editOptinText: '_editOptin',

    initComponent: function() {
        this.items = [{
            xtype: 'checkbox',
            itemId: 'addOptin',
            boxLabel: this.addOptinText,
            hideLabel: true,
            checked: true,
            name: 'optin',
            border: false,
            disabled: this.mode !== 'add',
            hidden: this.mode !== 'add',
            listeners: {
                check: function(c, checked) {
                    this.getComponent('passwordFieldset').setDisabled(checked);
                },
                scope: this
            }
        },{
            xtype: 'checkbox',
            itemId: 'editOptin',
            boxLabel: this.editOptinText,
            hideLabel: true,
            checked: false,
            name: 'optin',
            border: false,
            disabled: this.mode === 'add',
            hidden: this.mode === 'add',
            listeners: {
                check: function(c, checked) {
                    this.getComponent('passwordFieldset').setDisabled(checked);
                },
                scope: this
            }
        },{
            xtype: 'fieldset',
            itemId: 'passwordFieldset',
            title: this.passwordText,
            autoHeight: true,
            disabled: this.mode === 'add',
            items: [{
                xtype: 'ux.passwordmeterfield',
                itemId: 'password',
                name: 'password',
                fieldLabel: this.passwordText,
                minLength: Phlexible.Config.get('users.system.password_min_length'),
                width: 150
            },{
                xtype: 'fieldset',
                itemId: 'generateFieldset',
                title: this.generatePasswordText,
                items: [{
                    xtype: 'fieldcontainer',
                    itemId: 'generateContainer',
                    hideLabel: true,
                    layout: 'hbox',
                    items: [{
                        xtype: 'textfield',
                        itemId: 'generatedPassword',
                        emptyText: this.generatedPasswordText,
                        readOnly: true,
                        width: 150,
                        padding: '0 5 0 0'
                    },{
                        xtype: 'button',
                        itemId: 'generateBtn',
                        text: this.generateText,
                        iconCls: Phlexible.Icon.get('wand'),
                        handler: function(btn) {
                            var generator = Ext.create('Phlexible.user.util.PasswordGenerator'),
                                length = Phlexible.Config.get('users.system.password_min_length'),
                                password = generator.create(length, false);

                            this.getComponent('passswordFieldset').getComponent('generateFieldset').getComponent('generateContainer').getComponent('generatedPassword').setValue(password);
                            this.getComponent('passswordFieldset').getComponent('password').setValue(password);
                        },
                        scope: this
                    }]
                }]
            }]
        }];

        this.callParent(arguments);
    },

    loadUser: function(user) {
        this.getForm().loadRecord(user);
    },

    isValid: function() {
        return this.getForm().isValid();
    }
});
