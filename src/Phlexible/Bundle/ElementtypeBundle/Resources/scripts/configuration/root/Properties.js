Ext.define('Phlexible.elementtype.configuration.root.Properties', {
    extend: 'Ext.form.Panel',
    requires: [
        'Phlexible.gui.util.ImageSelectWindow',
        'Phlexible.metaset.model.MetaSet'
    ],
    xtype: 'elementtype.configuration.root.properties',

    iconCls: Phlexible.Icon.get('property'),
    autoScroll: true,
    border: false,
    defaultType: 'textfield',
    labelWidth: 120,
    padding: 5,
    defaultFocus: 'title',
    defaults: {
        anchor: '100%'
    },

    titleText: '_titleText',
    uniqueIdText: '_uniqueIdText',
    iconText: '_iconText',
    defaultTabText: '_defaultTabText',
    noDefaultTabText: '_noDefaultTabText',
    defaultContentTabText: '_defaultContentTabText',
    noDefaultContentTabText: '_noDefaultContentTabText',
    metasetText: '_metasetText',
    noMetasetText: '_noMetasetText',
    templateText: '_templateText',
    hideChildrenText: '_hideChildrenText',
    commentText: '_commentText',

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'textfield',
                name: 'title',
                fieldLabel: this.titleText,
                allowBlank: false
            },
            {
                xtype: 'textfield',
                name: 'uniqueId',
                fieldLabel: this.uniqueIdText,
                allowBlank: false,
                regex: /^[a-z0-9-_]+$/
            },
            {
                xtype: 'textfield',
                itemId: 'icon',
                name: 'icon',
                fieldLabel: this.iconText,
                triggers: {
                    select: {
                        handler: function () {
                            var www = Ext.create('Phlexible.gui.util.ImageSelectWindow', {
                                storeUrl: Phlexible.Router.generate('elementtypes_data_images'),
                                value: this.getComponent('icon').getValue(),
                                listeners: {
                                    imageSelect: function (image) {
                                        //Phlexible.console.log(image);

                                        this.getComponent('icon').setValue(image);
                                    },
                                    scope: this
                                }
                            });
                            www.show();
                        }
                    }
                }
            },
            {
                xtype: 'combobox',
                itemId: 'defaultTab',
                name: 'defaultTab',
                fieldLabel: this.defaultTabText,
                emptyText: this.noDefaultTabText,
                store: Ext.create('Ext.data.Store', {
                    fields: ['id', 'title'],
                    data: [
                        ['list', 'List'],
                        ['data', 'Data'],
                        ['preview', 'Preview'],
                        ['rights', 'Rights'],
                        ['links', 'Links'],
                        ['history', 'History'],
                        ['urls', 'Urls']
                    ]
                }),
                displayField: 'title',
                valueField: 'id',
                editable: false,
                mode: 'local',
                typeAhead: false,
                triggerAction: 'all',
                allowEmpty: true,
                triggers: {
                    clear: {
                        type: 'clear'
                    }
                }
            },
            {
                xtype: 'combobox',
                itemId: 'defaultContentTab',
                name: 'defaultContentTab',
                fieldLabel: this.defaultContentTabText,
                emptyText: this.noDefaultContentTabText,
                store: Ext.create('Ext.data.Store', {
                    fields: ['id', 'title'],
                    data: []
                }),
                forceSelection: true,
                displayField: 'title',
                valueField: 'id',
                editable: false,
                mode: 'local',
                typeAhead: false,
                triggerAction: 'all',
                allowEmpty: true,
                triggers: {
                    clear: {
                        type: 'clear'
                    }
                }
            },
            {
                xtype: 'combobox',
                itemId: 'metaset',
                name: 'metaset',
                fieldLabel: this.metasetText,
                emptyText: this.noMetasetText,
                store: Ext.create('Ext.data.Store', {
                    model: 'Phlexible.metaset.model.MetaSet',
                    proxy: {
                        type: 'ajax',
                        url: Phlexible.Router.generate('phlexible_api_metaset_get_metasets'),
                        reader: {
                            type: 'json',
                            rootProperty: 'sets',
                            idProperty: 'id'
                        }
                    },
                    autoLoad: true,
                    listeners: {
                        load: function() {
                            var value = this.getComponent('metaset').getValue();
                            if (value) {
                                this.getComponent('metaset').setValue(value);
                            }
                        },
                        scope: this
                    }
                }),
                displayField: 'name',
                valueField: 'id',
                editable: false,
                mode: 'remote',
                typeAhead: false,
                triggerAction: 'all',
                allowEmpty: true,
                triggers: {
                    clear: {
                        type: 'clear'
                    }
                }
            },
            {
                xtype: 'textfield',
                name: 'template',
                fieldLabel: this.templateText
            },
            {
                xtype: 'checkbox',
                name: 'hideChildren',
                hideEmptyLabel: false,
                boxLabel: this.hideChildrenText
            },
            {
                xtype: 'textarea',
                name: 'comment',
                fieldLabel: this.commentText,
                height: 100
            }
        ];

        if (Phlexible.User.isGranted('ROLE_SUPER_ADMIN')) {
            this.items.push({
                xtype: 'fieldset',
                title: 'Debug',
                autoHeight: true,
                collapsible: true,
                collapsed: true,
                defaults: {
                    anchor: '100%'
                },
                items: [
                    {
                        xtype: 'textarea',
                        name: 'debugDump',
                        fieldLabel: 'Dump',
                        flex: 1,
                        height: 300,
                        readOnly: true,
                        fieldStyle: {
                            'font-family': 'Courier, Courier New, monospace'
                        }
                    }
                ]
            });
        }
    },

    loadProperties: function(properties, node) {
        var values = {
            title: properties.title || '',
            uniqueId: properties.uniqueId || '',
            icon: properties.icon || '',
            defaultTab: properties.defaultTab || '',
            defaultContentTab: properties.defaultContentTab || '',
            metaset: properties.metaset || '',
            template: properties.template || '',
            hideChildren: properties.hideChildren || false,
            comment: properties.comment || ''
        };

        if (values.defaultContentTab !== null) {
            values.defaultContentTab += '';
        }

        if (Phlexible.User.isGranted('ROLE_SUPER_ADMIN')) {
            values.debugDump = JSON.stringify(node.data, function replacer(key, value) {
                if (key === 'children') {
                    return undefined;
                }
                return value;
            }, '  ');
        }

        this.getForm().setValues(values);
    },

    loadNode: function (node) {
        this.node = node;
        this.updateDefaultContentTabStore();

        this.loadProperties(node.get('properties'), node);
    },

    getSaveValues: function () {
        return this.getForm().getValues();
    },

    updateDefaultContentTabStore: function() {
        var store = this.getComponent('defaultContentTab').getStore();
        store.removeAll();
        Ext.each(this.node.childNodes, function(childNode, key) {
            store.add({
                id: key + '',
                title: childNode.text
            });
        });
    },

    isValid: function () {
        var valid = this.getForm().isValid(),
            defaultContentTab = this.getComponent('defaultContentTab').getValue();

        if (defaultContentTab) {
            this.updateDefaultContentTabStore();
            valid = valid && this.node.childNodes[defaultContentTab] !== undefined;
        }

        if (valid) {
            //this.header.child('span').removeClass('error');
            this.setIconCls(Phlexible.Icon.get('property'));

            return true;
        } else {
            //this.header.child('span').addClass('error');
            this.setIconCls(Phlexible.Icon.get('exclamation-red'));

            return false;
        }
    }
});
