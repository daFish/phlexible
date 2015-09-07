/**
 * User comment edit panel
 */
Ext.define('Phlexible.user.edit.Comment', {
    extend: 'Ext.form.FormPanel',
    xtype: 'user.edit-comment',

    iconCls: Phlexible.Icon.get('sticky-note-text'),
    bodyPadding: 10,
    layout: 'fit',
    defaultType: 'textfield',
    fieldDefaults:{
        labelWidth: 130,
        labelAlign: 'top',
        msgTarget: 'under'
    },

    key: 'comment',

    commentText: '_comment',

    initComponent: function() {
        this.items = [{
            xtype: 'textarea',
            name: 'comment',
            fieldLabel: this.commentText,
            emptyText: this.commentText
        }];

        this.callParent(arguments);
    },

    loadUser: function(user) {
        this.getComponent(0).setValue(user.get('comment'));
    },

    isValid: function() {
        return this.getForm().isValid();
    },

    applyToUser: function(user) {
        this.getForm().updateRecord(user);
    }
});
