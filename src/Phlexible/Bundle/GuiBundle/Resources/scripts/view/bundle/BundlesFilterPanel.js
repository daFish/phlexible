Ext.define('Phlexible.gui.bundles.BundlesFilterPanel', {
    extend: 'Ext.form.FormPanel',
    alias: 'widget.gui-bundles-filter',

    title: Phlexible.gui.Strings.filter,
    strings: Phlexible.gui.Strings,
    bodyPadding: 5,
    cls: 'p-gui-bundles-filter',
    iconCls: 'p-gui-filter-icon',
    autoScroll: true,

    initComponent: function () {
        this.initMyTasks();
        this.initMyItems();
        this.initMyDockedItems();
        this.loadFilterValues();

        this.callParent(arguments);
    },

    initMyTasks: function() {
        this.task = new Ext.util.DelayedTask(this.updateFilter, this);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'panel',
                title: this.strings.filter,
                layout: 'form',
                frame: true,
                defaults: {
                    hideLabel: true
                },
                items: [
                    {
                        xtype: 'textfield',
                        name: 'filter',
                        anchor: '-10',
                        enableKeyEvents: true,
                        listeners: {
                            keyup: function (field, event) {
                                if (event.getKey() == event.ENTER) {
                                    this.task.cancel();
                                    this.updateFilter();
                                    return;
                                }

                                this.task.delay(500);
                            },
                            scope: this
                        }
                    }
                ]
            },
            {
                xtype: 'panel',
                itemId: 'packages',
                title: this.strings['package'],
                margin: '5 0 0 0',
                layout: 'form',
                frame: true,
                collapsible: true,
                defaults: {
                    hideLabel: true
                },
                items: [{
                    border: false,
                    plain: true,
                    frame: false,
                    html: '<div class="loading-indicator">Loading...</div>'
                }]
            }
        ];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'bottom',
            ui: 'footer',
            items: [{
                xtype: 'component', flex: 1
            }, {
                xtype: 'button',
                text: this.strings.reset,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.RELOAD),
                disabled: true,
                handler: this.resetFilter,
                scope: this
            }]
        }];
    },

    loadFilterValues: function() {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('gui_bundles_filtervalues'),
            success: this.onLoadFilterValues,
            scope: this
        });
    },

    getPackagesForm: function() {
        return this.getComponent('packages');
    },

    onLoadFilterValues: function (response) {
        var data = Ext.decode(response.responseText);

        if (data.packages && data.packages.length && Ext.isArray(data.packages)) {
            this.getPackagesForm().removeAll();
            Ext.each(data.packages, function (item) {
                this.getPackagesForm().add({
                    xtype: 'checkbox',
                    name: 'package_' + item.id,
                    boxLabel: item.title,
                    checked: item.checked,
                    listeners: {
                        change: this.updateFilter,
                        scope: this
                    }
                });
            }, this);
        }
    },

    resetFilter: function () {
        this.getForm().reset();
        this.updateFilter(true);
        this.getDockedComponent(0).getComponent(1).disable();
    },

    updateFilter: function (noEnable) {
        if (!noEnable) {
            this.getDockedComponent(0).getComponent(1).enable();
        }

        var values = this.getValues();

        var data = {
            status: [],
            packages: [],
            filter: ''
        };

        for (var key in values) {
            if (!values.hasOwnProperty(key)) continue;

            //if (values[key] !== 'on') continue;

            if (key.substr(0, 8) == 'package_' && values[key] === 'on') {
                data.packages.push(key.substr(8));
            }
            else if (key === 'filter') {
                data.filter = values[key];
            }
        }

        this.fireEvent('updateFilter', data);
    }
});
