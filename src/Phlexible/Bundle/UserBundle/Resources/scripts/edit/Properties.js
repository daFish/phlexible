/**
 * User properties edit panel
 */
Ext.define('Phlexible.user.edit.Properties', {
    extend: 'Ext.grid.property.Grid',
    alias: 'widget.user-edit-properties',

    title: '_properties',
    iconCls: Phlexible.Icon.get('property'),
    border: true,
    hideMode: 'offsets',
    viewConfig: {
        stripeRows: true
    },
    source: {x:1},
    nameColumnWidth: 200,

    key: 'properties',

    addPropertyText: '_add_property',
    removePropertyText: '_remove_property',
    newPropertyText: '_new_property',
    enterPropertyNameText: '_enter_property_name',

    initComponent: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'top',
            itemId: 'tbar',
            items: [{
                xtype: 'button',
                text: this.addPropertyText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                handler: this.addProperty,
                scope: this
            },{
                xtype: 'button',
                text: this.removePropertyText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                disabled: true,
                handler: this.removeProperty,
                scope: this
            }]
        }];

        this.on({
            selectionchange: function(grid, selected) {
                if (selected.length) {
                    this.getDockedComponent('tbar').getComponent(1).enable();
                } else {
                    this.getDockedComponent('tbar').getComponent(1).disable();
                }
            },
            scope: this
        });

        this.callParent();
    },

    addProperty: function() {
        Ext.MessageBox.prompt(this.newPropertyText, this.enterPropertyNameText, function(btn, text) {
            if (btn === 'ok') {
                var source = this.getSource();

                source[text] = '';

                this.setSource(source);
            }
        }, this);
    },

    removeProperty: function() {
        var selection = this.getSelectionModel().getSelection(),
            source = this.getSource(),
            name;

        if (!selection.length) {
            return;
        }

        name = selection[0].get('name');
        delete source[name];

        this.setSource(source);
    },

    loadRecord: function(record) {
        this.setSource(record.get('properties'));
    },

    isValid: function() {
        return true;
    },

    getValues: function() {
        var modifiedRecords = this.getStore().getRange(),
            data = {},
            i;

        for(i = 0; i < modifiedRecords.length; i += 1) {
            data[modifiedRecords[i].get('name')] = modifiedRecords[i].get('value');
        }

        return data;
    }
});
