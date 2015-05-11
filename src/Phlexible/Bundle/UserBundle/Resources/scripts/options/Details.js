/**
 * User details option panel
 */
Ext.define('Phlexible.user.options.Details', {
    extend: 'Ext.form.FormPanel',
    xtype: 'user.options-details',

    iconCls: Phlexible.Icon.get('card-address'),
    bodyPadding: '15',
    border: true,
    hideMode: 'offsets',
    defaultType: 'textfield',
    fieldDefaults: {
        labelWidth: 150,
        labelAlign: 'top',
        msgTarget: 'under'
    },
    monitorValid: true,

    descriptionText: '_description',
    firstnameText: '_firstname',
    lastnameText: '_lastname',
    emailText: '_email',
    imageText: '_image',
    saveText: '_save',
    cancelText: '_cancel',

    initComponent: function() {
        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        var user = Phlexible.User;

        this.items = [{
            fieldLabel: this.firstnameText,
            name: 'firstname',
            allowBlank: false,
            value: user.getFirstname(),
            anchor: '100%'
        }, {
            fieldLabel: this.lastnameText,
            name: 'lastname',
            allowBlank: false,
            value: user.getLastname(),
            anchor: '100%'
        }, {
            fieldLabel: this.emailText,
            name: 'email',
            allowBlank: false,
            value: user.getEmail(),
            vtype: 'email',
            anchor: '100%'
        }, {
            xtype: 'label',
            text: this.imageText + ':'
        }, {
            xtype: 'container',
            border: false,
            html: '<img width="80" height="80" style="margin-top: 10px; border: 1px solid #99bce8;" src="http://www.gravatar.com/avatar/' + user.getEmailHash() + '?s=80&d=mm" />'
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
                    this.form.submit({
                        url: Phlexible.Router.generate('phlexible_options'),
                        method: 'PATCH',
                        success: function(form, result) {
                            if (result.success) {
                                var values = form.getValues(),
                                    user = Phlexible.User;
                                user.setFirstname(values.firstname);
                                user.setLastname(values.lastname);
                                user.setEmail(values.email);
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
