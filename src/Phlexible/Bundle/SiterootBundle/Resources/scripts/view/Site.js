Ext.define('Phlexible.site.view.Site', {
    extend: 'Ext.panel.Panel',

    xtype: 'site.site',

    border: false,

    defaultText: '_defaultText',
    titlesText: '_titlesText',
    hostnameText: '_hostnameText',
    lastText: '_lastText',
    createdByText: '_createdByText',
    createdAtText: '_createdAtText',
    modifiedByText: '_modifiedByText',
    modifiedAtText: '_modifiedAtText',

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        var languageItems = [];

        Ext.each(Phlexible.Config.get('set.language.frontend'), function(language) {
            languageItems.push({
                fieldLabel: Phlexible.Icon.inline(language[2]) + ' ' + language[1],
                name: 'titles[' + language[0] + ']',
                xtype: 'textfield',
                flex: 1,
                allowBlank: false,
                bind: {
                    value: '{list.selection.titles.' + language[0] + '}'
                }
            });
        }, this);

        this.items = [
            {
                xtype: 'form',
                itemId: 'titles',
                border: false,
                padding: 5,
                xlabelAlign: 'top',
                items: [{
                    xtype: 'textfield',
                    fieldLabel: this.hostnameText,
                    name: 'hostname',
                    bind: {
                        value: '{list.selection.hostname}'
                    }
                },{
                    xtype: 'checkbox',
                    fieldLabel: this.defaultText,
                    name: 'default',
                    bind: {
                        value: '{list.selection.default}'
                    }
                },{
                    xtype: 'fieldset',
                    title: this.titlesText,
                    items: languageItems
                },{
                    xtype: 'fieldset',
                    title: this.lastText,
                    items: [{
                        xtype: 'displayfield',
                        fieldLabel: this.createdByText,
                        bind: {
                            value: '{list.selection.createdBy}'
                        }
                    },{
                        xtype: 'displayfield',
                        fieldLabel: this.createdAtText,
                        bind: {
                            value: '{list.selection.createdAt}'
                        }
                    },{
                        xtype: 'displayfield',
                        fieldLabel: this.modifiedByText,
                        bind: {
                            value: '{list.selection.modifiedBy}'
                        }
                    },{
                        xtype: 'displayfield',
                        fieldLabel: this.modifiedAtText,
                        bind: {
                            value: '{list.selection.modifiedAt}'
                        }
                    }]
                }]
            }
        ];
    },

    isValid: function () {
        var valid = this.getComponent('titles').getForm().isValid();

        if (valid) {
            this.header.child('span').removeClass('error');
        } else {
            this.header.child('span').addClass('error');
        }

        return valid;
    },

    /**
     * Get the data to be saved.
     */
    getSaveData: function () {
        return this.getComponent('titles').getForm().getValues();
    }
});
