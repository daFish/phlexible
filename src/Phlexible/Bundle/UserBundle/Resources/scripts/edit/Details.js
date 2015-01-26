/**
 * User details edit panel
 */
Ext.define('Phlexible.user.edit.Details', {
    extend: 'Ext.form.FormPanel',
    alias: 'widget.user-edit-details',

    title: '_details',
    iconCls: Phlexible.Icon.get('card-address'),
    bodyPadding: '5',
    border: true,
    hideMode: 'offsets',
    defaultType: 'textfield',
    fieldDefaults:{
        labelWidth: 130,
        labelAlign: 'top',
        msgTarget: 'under'
    },

    key: 'details',

    usernameText: '_firstname',
    firstnameText: '_firstname',
    lastnameText: '_lastname',
    emailText: '_email',
    imageText: '_image',
    saveText: '_save',
    cancelText: '_cancel',

    initComponent: function() {
        this.items = [{
            fieldLabel: this.firstnameText,
            name: 'firstname',
            allowBlank: false,
            anchor: '100%'
        },{
            fieldLabel: this.lastnameText,
            name: 'lastname',
            allowBlank: false,
            anchor: '100%'
        },{
            fieldLabel: this.usernameText,
            name: 'username',
            allowBlank: false,
            anchor: '100%'
        },{
            fieldLabel: this.emailText,
            name: 'email',
            allowBlank: false,
            vtype: 'email',
            anchor: '100%'
        },{
            xtype: 'label',
            text: this.imageText + ':'
        },{
            xtype: 'container',
            border: false,
            html: '<img width="80" height="80" style="margin-top: 10px; border: 1px solid #99bce8;" src="" />'
        }];

        this.callParent(arguments);
    },

    loadRecord: function(record) {
        this.getForm().loadRecord(record);

        var src = 'http://www.gravatar.com/avatar/' + record.get('emailHash') + '?s=80&d=mm';

        if (this.getComponent(5).rendered) {
            this.getComponent(5).el.down('img').set({
                src: src
            });
        } else {
            this.items[5].html = '<img width="80" height="80" style="margin-top: 10px; border: 1px solid #99bce8;" src="' + src + '" />';
        }
    },

    isValid: function() {
        return this.getForm().isValid();
    }
});
