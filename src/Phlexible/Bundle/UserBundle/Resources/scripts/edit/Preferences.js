/**
 * User preferences edit panel
 */
Ext.define('Phlexible.user.edit.Preferences', {
    extend: 'Ext.form.FormPanel',
    requires: [
        'Phlexible.gui.model.KeyValue'
    ],
    xtype: 'user.edit-preferences',

    iconCls: Phlexible.Icon.get('switch'),
    bodyPadding: 10,
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
                data: Phlexible.Config.get('set.themes')
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

    loadUser: function(user) {
        var properties = user.get('properties') || {};

        this.getForm().setValues({
            dateFormat: properties['preferences.dateFormat'],
            theme: properties['preferences.theme']
        });
    },

    isValid: function() {
        return this.getForm().isValid();
    }
});
