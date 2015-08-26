/**
 * User preferences option panel
 */
Ext.define('Phlexible.user.options.Preferences', {
    extend: 'Ext.form.FormPanel',
    xtype: 'user.options-preferences',

    iconCls: Phlexible.Icon.get('switch'),
    bodyPadding: '15',
    border: true,
    defaultType: 'textfield',
    fieldDefaults:{
        labelWidth: 150,
        labelAlign: 'top',
        msgTarget: 'under'
    },

    descriptionText: '_description',
    dateFormatText: '_date_format',
    systemDefaultText: '_system_default',
    themeText: '_theme',
    saveText: '_save',
    cancelText: '_cancel',

    initComponent: function() {
        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [{
            xtype: 'combo',
            name: 'theme',
            fieldLabel: this.themeText,
            value: Phlexible.User.getProperty('theme', 'classic'),
            store: Ext.create('Ext.data.Store', {
                model: 'Phlexible.gui.model.KeyValue',
                data: Phlexible.Config.get('set.themes')
            }),
            displayField: 'value',
            valueField: 'key',
            queryMode: 'local',
            editable: false
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
                handler: function() {
                    var values = this.getForm().getValues();

                    if (Phlexible.User.getProperty('theme', 'classic') !== values.theme) {
                        Phlexible.User.setProperty('theme', values.theme);
                        Phlexible.User.commit();
                    }
                },
                scope: this
            }]
        }];
    }
});
