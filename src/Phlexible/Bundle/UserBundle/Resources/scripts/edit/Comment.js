/**
 * User comment edit panel
 */
Ext.define('Phlexible.user.edit.Comment', {
    extend: 'Ext.form.FormPanel',
    alias: 'widget.user-edit-comment',

    title: '_comment',
    iconCls: Phlexible.Icon.get('sticky-note-text'),
    layout: 'fit',
    bodyPadding: 5,
    border: true,
    hideMode: 'offsets',
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
            hideLabel: true,
            emptyText: this.commentText
        }];

        this.callParent(arguments);
    },

    loadRecord: function(record) {
        this.getComponent(0).setValue(record.data.comment);
    },

    isValid: function() {
        return this.getForm().isValid();
    }
});
