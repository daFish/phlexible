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
    saveText: '_save',
    cancelText: '_cancel',

    initComponent: function() {
        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [];
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
                    this.form.submit({
                        url: Phlexible.Router.generate('phlexible_options'),
                        method: 'PATCH',
                        success: function(form, result) {
                            if (result.success) {
                                var values = form.getValues();
                                Phlexible.User.getOptions().language   = values.language;
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
