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

    initComponent: function() {
        this.items = [{
            xtype: 'ux.passwordmeterfield',
            itemId: 'password',
            name: 'password',
            fieldLabel: this.passwordText,
            minLength: Phlexible.Config.get('users.system.password_min_length'),
            width: 150,
            listeners: {
                change: function() {
                    if(this.mode === 'add') {
                        return;
                    }
                    this.getComponent('notify').enable();
                },
                scope: this
            }
        },{
            name: 'repeat',
            itemId: 'passwordRepeat',
            fieldLabel: this.passwordRepeatText,
            inputType: "password",
            minLength: Phlexible.Config.get('users.system.password_min_length'),
            width: 150,
            invalidText: this.passwordsDontMatchText,
            listeners: {
                change: function() {
                    if(this.mode === 'add') {
                        return;
                    }
                    this.getComponent(2).enable();
                },
                scope: this
            }
        },{
            xtype: 'checkbox',
            itemId: 'notify',
            name: 'notify',
            hideLabel: true,
            boxLabel: this.notifyUserText,
            disabled: true
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
                    itemId: 'generated',
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

                        this.getComponent('generateFieldset').getComponent('generateContainer').getComponent('generated').setValue(password);

                        this.getComponent('password').setValue(password);
                        this.getComponent('passwordRepeat').setValue(password);

                        if (this.mode !== 'add') {
                            this.getComponent('notify').enable();
                        }
                    },
                    scope: this
                }]
            }]
        }];

        this.callParent(arguments);
    },

    loadUser: function(user) {
        if (this.mode === 'add') {
            this.getComponent('notify').hide();
        }

        this.getForm().loadRecord(user);
    },

    isValid: function() {
        return this.getForm().isValid();
    }
});
