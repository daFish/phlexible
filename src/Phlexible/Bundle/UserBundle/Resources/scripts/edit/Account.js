/**
 * User account edit panel
 */
Ext.define('Phlexible.user.edit.Account', {
    extend: 'Ext.form.FormPanel',
    alias: 'widget.user-edit-account',
    requires: ['Ext.ux.form.trigger.Clear'],

    title: '_account',
    iconCls: Phlexible.Icon.get('key'),
    bodyPadding: '5',
    border: true,
    hideMode: 'offsets',
    defaultType: 'textfield',
    fieldDefaults:{
        labelWidth: 130,
        labelAlign: 'top',
        msgTarget: 'under'
    },

    key: 'account',

    accountText: '_account',
    isDisabledText: '_disabled',
    cantChangePasswordText: '_cant_change_password',
    passwordDoesntExpireText: '_password_doesnt_expire',
    changePasswordNextLoginText: '_change_password_next_login',
    accountExpiresOnText: '_account_expires_on',
    expireHelpText: '_expire_help',

    initComponent: function() {
        this.items = [{
            xtype: 'checkbox',
            boxLabel: this.isDisabledText,
            hideLabel: true,
            name: 'disabled',
            inputValue: '1',
            uncheckedValue: '0'
        },{
            xtype: 'checkbox',
            boxLabel: this.cantChangePasswordText,
            hideLabel: true,
            name: 'noPasswordChange',
            inputValue: '1',
            uncheckedValue: '0'
        },{
            xtype: 'checkbox',
            boxLabel: this.passwordDoesntExpireText,
            hideLabel: true,
            name: 'noPasswordExpire',
            inputValue: '1',
            uncheckedValue: '0'
        },{
            xtype: 'checkbox',
            boxLabel: this.changePasswordNextLoginText,
            hideLabel: true,
            name: 'forcePasswordChange',
            inputValue: '1',
            uncheckedValue: '0'
        },{
            xtype: 'datefield',
            fieldLabel: this.accountExpiresOnText,
            name: 'expiresAt',
            width: 150,
            format: 'Y-m-d',
            helpText: this.expireHelpText,
            triggers: {
                clear: {
                    type: 'clear'
                }
            }
        }];

        this.callParent(arguments);
    },

    loadRecord: function(record) {
        var properties = record.get('properties') || {};

        this.getForm().setValues({
            disabled: record.get('disabled'),
            expiresAt: record.get('expiresAt'),
            noPasswordChange: properties['account.noPasswordChange'],
            noPasswordExpire: properties['account.noPasswordExpire'],
            forcePasswordChange: properties['account.forcePasswordChange']
        });
    },

    isValid: function() {
        return this.getForm().isValid();
    }
});
