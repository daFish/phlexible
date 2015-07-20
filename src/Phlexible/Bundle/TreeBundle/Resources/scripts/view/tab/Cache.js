Ext.provide('Phlexible.tree.view.tab.Cache');

Phlexible.tree.view.tab.Cache = Ext.extend(Ext.form.FormPanel, {
    title: Phlexible.elements.Strings.cache.cache,
    iconCls: 'p-element-cache-icon',
    strings: Phlexible.elements.Strings.cache,
    bodyStyle: 'margin: 5px',

    initComponent: function () {
        this.element.on({
            load: this.onLoadElement,
            internalSave: this.onInternalSave,
            scope: this
        });

        this.items = [{
            xtype: 'textfield',
            name: 'expires',
            fieldLabel: this.strings.expires,
            width: 300
        },{
            xtype: 'checkbox',
            name: 'public',
            labelSeparator: '',
            fieldLabel: '',
            boxLabel: this.strings.public
        },{
            xtype: 'numberfield',
            name: 'maxage',
            fieldLabel: this.strings.maxage,
            width: 300
        },{
            xtype: 'numberfield',
            name: 'smaxage',
            fieldLabel: this.strings.smaxage,
            width: 300
        },{
            xtype: 'textfield',
            name: 'lastModified',
            fieldLabel: this.strings.last_modified,
            width: 300
        },{
            xtype: 'textfield',
            name: 'ETag',
            fieldLabel: this.strings.etag,
            width: 300
        },{
            xtype: 'textfield',
            name: 'vary',
            fieldLabel: this.strings.vary,
            width: 300
        }];

        Phlexible.tree.view.tab.Cache.superclass.initComponent.call(this);
    },

    onLoadElement: function (element) {
        if (element.getElementtypeType() !== 'full') {
            this.disable();
            //this.hide();

            this.getForm().reset();

            return;
        }

        this.enable();
        //this.show();

        this.getForm().reset();

        this.getForm().setValues(this.element.getConfiguration().cache || {});
    },

    onInternalSave: function (parameters, errors) {
        if (!this.getForm().isValid()) {
            errors.push('Required fields are missing.');
            return false;
        }

        parameters.cache = Ext.encode(this.getForm().getValues());
    }
});

Ext.reg('tree-tab-cache', Phlexible.tree.view.tab.Cache);
