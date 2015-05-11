Ext.define('Phlexible.siteroot.view.Titles', {
    extend: 'Ext.panel.Panel',

    xtype: 'siteroot.titles',

    border: false,

    customTitlesText: '_customTitlesText',
    nameText: '_nameText',
    patternText: '_patternText',
    exampleText: '_exampleText',
    legendText: '_legendText',

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'form',
                itemId: 'titles',
                border: false,
                padding: 5,
                xlabelAlign: 'top',
                items: []
            }
        ];

        Ext.each(Phlexible.Config.get('set.language.frontend'), function(language) {
            this.items[0].items.push({
                fieldLabel: Phlexible.Icon.inline(language[2]) + ' ' + language[1],
                name: language[0],
                xtype: 'textfield',
                flex: 1,
                allowBlank: false,
                bind: {
                    value: '{list.selection.titles.' + language[0] + '}'
                }
            });
        }, this);
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
        return {
            titles: this.getComponent('titles').getForm().getValues()
        };
    }
});
