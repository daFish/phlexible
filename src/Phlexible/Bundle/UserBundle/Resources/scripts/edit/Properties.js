/**
 * User properties edit panel
 */
Ext.define('Phlexible.user.edit.Properties', {
    extend: 'Ext.grid.property.Grid',
    xtype: 'user.edit-properties',

    iconCls: Phlexible.Icon.get('property'),
    viewConfig: {
        stripeRows: true,
        deferEmptyText: false
    },
    source: {x:1},
    nameColumnWidth: 200,

    key: 'properties',

    emptyText: '_emptyText',
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
                itemId: 'addBtn',
                text: this.addPropertyText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                handler: this.addProperty,
                scope: this
            },{
                xtype: 'button',
                itemId: 'removeBtn',
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
                    this.getDockedComponent('tbar').getComponent('removeBtn').enable();
                } else {
                    this.getDockedComponent('tbar').getComponent('removeBtn').disable();
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

    loadUser: function(user) {
        this.setSource(Ext.clone(user.get('properties')));
    },

    isValid: function() {
        return true;
    },

    applyToUser: function(user) {
        user.set('properties', this.getSource());
    }
});
