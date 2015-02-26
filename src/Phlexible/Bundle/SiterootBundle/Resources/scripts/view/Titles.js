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

        for (var i = 0; i < Phlexible.Config.get('set.language.frontend').length; i++) {
            this.items[0].items.push({
                fieldLabel: Phlexible.Icon.inline(Phlexible.Config.get('set.language.frontend')[i][2]) + ' ' + Phlexible.Config.get('set.language.frontend')[i][1],
                name: Phlexible.Config.get('set.language.frontend')[i][0],
                xtype: 'textfield',
                flex: 1,
                allowBlank: false
            });
        }
    },

    /**
     * After the siteroot selection changes load the siteroot data.
     *
     * @param {Phlexible.siteroot.model.Siteroot} siteroot
     */
    loadData: function (siteroot) {
        this.getComponent('titles').getForm().reset();
        this.getComponent('titles').getForm().setValues(siteroot.data.titles);

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
