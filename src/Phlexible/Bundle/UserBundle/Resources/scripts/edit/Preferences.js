/**
 * User preferences edit panel
 */
Ext.define('Phlexible.user.edit.Preferences', {
    extend: 'Ext.form.FormPanel',
    alias: 'widget.user-edit-preferences',

    title: '_preferences',
    iconCls: Phlexible.Icon.get('switch'),
    bodyPadding: '5',
    border: true,
    hideMode: 'offsets',
    defaultType: 'textfield',
    fieldDefaults:{
        labelWidth: 130,
        labelAlign: 'top',
        msgTarget: 'under'
    },

    key: 'preferences',

    themeText: '_theme',
    dateText: '_date',
    systemDefaultText: '_system_default',

    initComponent: function() {
        this.items = [{
            xtype: 'combo',
            fieldLabel: this.themeText,
            name: 'theme',
            anchor: '100%',
            emptyText: this.systemDefaultText,
            store: Ext.create('Ext.data.Store', {
                model: 'Phlexible.gui.model.KeyValue',
                data: Phlexible.App.getConfig().get('resources.themes')
            }),
            displayField: 'value',
            valueField: 'key',
            queryMode: 'local',
            //triggerAction: 'all',
            //selectOnFocus: true,
            editable: false
        }];

        this.callParent(arguments);
    },

    loadRecord: function(record) {
        var properties = record.get('properties') || {};

        this.getForm().setValues({
            dateFormat: properties['preferences.dateFormat'],
            theme: properties['preferences.theme']
        });
    },

    isValid: function() {
        return this.getForm().isValid();
    }
});
