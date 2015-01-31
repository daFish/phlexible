/**
 * User password edit panel
 */
Ext.define('Phlexible.user.edit.Password', {
    extend: 'Ext.form.FormPanel',
    alias: 'widget.user-edit-password',

    title: '_password',
    iconCls: Phlexible.Icon.get('star'),
    bodyPadding: '5',
    border: true,
    hideMode: 'offsets',
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
            name: 'password',
            fieldLabel: this.passwordText,
            minLength: Phlexible.Config.get('users.system.password_min_length'),
            width: 150,
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
            name: 'repeat',
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
            name: 'notify',
            hideLabel: true,
            boxLabel: this.notifyUserText,
            disabled: true
        },{
            xtype: 'fieldset',
            title: this.generatePasswordText,
            items: [{
                xtype: 'fieldcontainer',
                hideLabel: true,
                layout: 'hbox',
                items: [{
                    xtype: 'textfield',
                    emptyText: this.generatedPasswordText,
                    readOnly: true,
                    width: 150,
                    padding: '0 5 0 0'
                },{
                    xtype: 'button',
                    text: this.generateText,
                    iconCls: Phlexible.Icon.get('wand'),
                    handler: function(btn) {
                        var generator = Ext.create('Phlexible.user.util.PasswordGenerator'),
                            length = Phlexible.Config.get('users.system.password_min_length'),
                            password = generator.create(length, false);

                        this.getComponent(3).getComponent(0).getComponent(0).setValue(password);

                        this.getComponent(0).setValue(password);
                        this.getComponent(1).setValue(password);

                        if (this.mode !== 'add') {
                            this.getComponent(2).enable();
                        }
                    },
                    scope: this
                }]
            }]
        }];

        this.callParent(arguments);
    },

    loadRecord: function(record) {
        if (this.mode === 'add') {
            this.getComponent(2).hide();
        }

        this.getForm().loadRecord(record);
    },

    isValid: function() {
        return this.getForm().isValid();
    }
});
