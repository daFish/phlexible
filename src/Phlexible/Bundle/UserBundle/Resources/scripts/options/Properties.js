/**
 * User properties option panel
 */
Ext.define('Phlexible.user.options.Properties', {
    extend: 'Ext.form.FormPanel',
    alias: 'widget.user-options-properties',

    title: '_properties', //MWF.strings.Users.personal_details,
    //bodyStyle: 'padding: 15px',
    border: true,
    hideMode: 'offsets',
    labelWidth: 150,

    descriptionText: '_description',
    saveText: '_save',
    cancelText: '_cancel',

    initComponent: function() {
        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.source = [{}];
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
