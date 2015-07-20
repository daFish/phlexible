Ext.provide('Phlexible.tree.view.tab.Configuration');

Phlexible.tree.view.tab.Configuration = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.elements.Strings.configuration,
    title: Phlexible.elements.Strings.configuration.configuration,
    cls: 'p-elements-page-accordion',
    iconCls: 'p-element-action-icon',
    border: false,
    autoHeight: true,
    labelWidth: 100,
    bodyStyle: 'padding: 5px',
    labelAlign: 'top',

    key: 'configuration',

    initComponent: function () {
        this.element.on({
            load: this.onLoadElement,
            internalSave: this.onInternalSave,
            scope: this
        });

        this.populateItems();

        Phlexible.tree.view.tab.Configuration.superclass.initComponent.call(this);
    },

    populateItems: function () {
        this.items = [
            {
                // 0
                xtype: 'checkbox',
                name: 'navigation',
                hideLabel: true,
                boxLabel: this.strings.in_navigation
            },{
                // 1
                xtype: 'textfield',
                name: 'template',
                fieldLabel: this.strings.template
            },
            {
                // 2
                xtype: 'checkbox',
                name: 'robotsNoIndex',
                fieldLabel: this.strings.robots,
                boxLabel: this.strings.robots_no_index
            },
            {
                // 3
                xtype: 'checkbox',
                name: 'robotsNoFollow',
                hideLabel: true,
                boxLabel: this.strings.robots_no_follow
            },
            {
                // 4
                xtype: 'checkbox',
                name: 'searchNoIndex',
                fieldLabel: this.strings.internal_search,
                boxLabel: this.strings.search_no_index
            }
        ];
    },

    onLoadElement: function (element) {
        if (element.getElementtypeType() !== 'full' && element.getElementtypeType() !== 'part') {
            this.disable();
            //this.hide();

            this.getForm().reset();

            return;
        }

        this.enable();
        //this.show();

        this.getForm().reset();

        this.getForm().setValues({
            navigation: element.getConfiguration().navigation || false,
            template: element.getConfiguration().template || '',
            robotsNoIndex: element.getConfiguration().robotsNoIndex || false,
            robotsNoFollow: element.getConfiguration().robotsNoFollow || false,
            searchNoIndex: element.getConfiguration().searchNoIndex || false
        });

        if (element.getElementtypeType() === 'part') {
            this.getComponent(0).hide();
            this.getComponent(1).show();
            this.getComponent(2).hide();
            this.getComponent(3).hide();
            this.getComponent(4).hide();
        } else {
            this.getComponent(0).show();
            this.getComponent(1).show();
            this.getComponent(2).show();
            this.getComponent(3).show();
            this.getComponent(4).show();
        }

        this.show();
    },

    onInternalSave: function (parameters, errors) {
        if (!this.getForm().isValid()) {
            errors.push('Required fields are missing.');
            return false;
        }

        var values = this.getForm().getValues();
        parameters.template = values.template;
        if (element.getElementtypeType() !== 'part') {
            parameters.navigation = values.navigation;
            parameters.robotsNoIndex = values.robotsNoIndex;
            parameters.robotsNoFollow = values.robotsNoFollow;
            parameters.searchNoIndex = values.searchNoIndex;
        }
    }
});

Ext.reg('tree-tab-configuration', Phlexible.tree.view.tab.Configuration);
