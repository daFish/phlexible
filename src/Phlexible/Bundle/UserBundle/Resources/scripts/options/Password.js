/**
 * User password option panel
 */
Ext.define('Phlexible.user.options.Password', {
    extend: 'Ext.form.FormPanel',
    xtype: 'user.options-password',

    iconCls: Phlexible.Icon.get('star'),
    bodyPadding: '15',
    border: true,
    hideMode: 'offsets',
    defaultType: 'textfield',
    fieldDefaults: {
        labelWidth: 150,
        labelAlign: 'top',
        msgTarget: 'under'
    },
    monitorValid: true,

    descriptionText: '_description',
    passwordText: '_password',
    passwordRepeatText: '_password_repeat',
    passwordsDontMatchText: '_passwords_dont_match_text',
    saveText: '_save',
    cancelText: '_cancel',

    initComponent: function() {
        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [{
            xtype: 'ux.passwordmeterfield',
            fieldLabel: this.passwordText,
            name: 'password',
            inputType: 'password',
            anchor: '100%',
            width: 200,
            value: '',
            minLength: Phlexible.Config.get('users.system.password_min_length'),
            strength   : 24
        },{
            fieldLabel: this.passwordRepeatText,
            name: 'password_repeat',
            inputType: 'password',
            anchor: '100%',
            width: 200,
            minLength: Phlexible.Config.get('users.system.password_min_length'),
            listeners: {
                valid: function(f) {
                    f.ownerCt.getComponent(0).validate();
                },
                scope: this
            }
        }];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'bottom',
            ui: 'footer',
            items: [{
                text: this.saveText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.SAVE),
                formBind: true,
                handler: function() {
                    this.form.submit({
                        url: Phlexible.Router.generate('phlexible_options'),
                        method: 'PATCH',
                        success: function(form, result) {
                            if (result.success) {

                            } else {
                                Phlexible.Notify.failure(result.msg);
                            }
                        },
                        scope: this
                    });
                },
                scope: this
            }]
        }];
    }
});
