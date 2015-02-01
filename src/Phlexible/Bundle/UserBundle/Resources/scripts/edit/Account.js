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

    accountText: '_accountText',
    enabledText: '_enabledText',
    lockedText: '_lockedText',
    expiredText: '_expiredText',
    credentialsExpiredText: '_credentialsExpiredText',
    expiresAtText: '_expiresAtText',
    credentialsExpireAtText: '_credentialsExpireAtText',

    initComponent: function() {
        this.items = [{
            xtype: 'checkbox',
            boxLabel: this.enabledText,
            hideLabel: true,
            name: 'enabled',
            inputValue: '1',
            uncheckedValue: '0'
        },{
            xtype: 'checkbox',
            boxLabel: this.lockedText,
            hideLabel: true,
            name: 'locked',
            inputValue: '1',
            uncheckedValue: '0'
        },{
            xtype: 'checkbox',
            boxLabel: this.expiredText,
            hideLabel: true,
            name: 'expired',
            inputValue: '1',
            uncheckedValue: '0'
        },{
            xtype: 'datefield',
            fieldLabel: this.expiresAtText,
            name: 'expiresAt',
            width: 160,
            format: 'Y-m-d H:i:s',
            triggers: {
                clear: {
                    type: 'clear'
                }
            }
        },{
            xtype: 'checkbox',
            boxLabel: this.credentialsExpiredText,
            hideLabel: true,
            name: 'credentialsExpired',
            inputValue: '1',
            uncheckedValue: '0'
        },{
            xtype: 'datefield',
            fieldLabel: this.credentialsExpireAtText,
            name: 'credentialsExpireAt',
            width: 160,
            format: 'Y-m-d H:i:s',
            triggers: {
                clear: {
                    type: 'clear'
                }
            }
        }];

        this.callParent(arguments);
    },

    loadRecord: function(user) {
        this.getForm().setValues(user.data);
    },

    isValid: function() {
        return this.getForm().isValid();
    }
});
