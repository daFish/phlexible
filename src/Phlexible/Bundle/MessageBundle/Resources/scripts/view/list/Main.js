Ext.define('Phlexible.message.view.list.Main', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Phlexible.message.view.list.Filter',
        'Phlexible.message.view.list.List',
        'Phlexible.message.view.list.MainController',
        'Phlexible.message.model.Message'
    ],
    xtype: 'message.list.main',
    controller: 'message.list.main',

    cls: 'p-message-list-main',
    iconCls: Phlexible.Icon.get('application-list'),
    layout: 'border',
    border: false,
    referenceHolder: true,
    viewModel: {
        stores: {
            messages: {
                //type: 'buffered',
                model: 'Message',
                autoLoad: true,
                remoteSort: true,
                //leadingBufferZone: 300,
                //pageSize: 100,
                pageSize: 25,
                sorters: [{
                    property: 'createdAt',
                    direction: 'DESC'
                }],
                listeners: {
                    load: 'updateFacets'
                }
            }
        }
    },

    subjectText: '_subjectText',
    bodyText: '_bodyText',
    typeText: '_typeText',
    channelText: '_channelText',
    roleText: '_roleText',
    userText: '_userText',
    createdAtText: '_createdAtText',

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'message.list.filter',
                itemId: 'filter',
                region: 'west',
                width: 250,
                padding: '5 0 5 5',
                collapsible: true,
                listeners: {
                    updateFilter: 'updateFilter'
                }
            },
            {
                region: 'center',
                itemId: 'listWrap',
                layout: 'border',
                border: false,
                items: [{
                    xtype: 'message.list.list',
                    itemId: 'list',
                    region: 'center',
                    reference: 'list',
                    padding: 5,
                    bind: '{messages}'
                    //selModel: {
                    //    pruneRemoved: false
                    //}
                },{
                    region: 'south',
                    xtype: 'form',
                    padding: '0 5 5 5',
                    height: 225,
                    bodyPadding: 5,
                    border: true,
                    bind: {
                        iconCls: '{list.selection.typeIconCls}',
                    },
                    iconCls: Phlexible.message.TypeIcons.info,
                    items: [
                        {
                            xtype: 'textfield',
                            name: 'body',
                            fieldLabel: this.subjectText,
                            anchor: '100%',
                            bind: {
                                value: '{list.selection.subject}'
                            },
                            readonly: true
                        },
                        {
                            xtype: 'textarea',
                            name: 'body',
                            fieldLabel: this.bodyText,
                            anchor: '100%',
                            height: 100,
                            bind: {
                                value: '{list.selection.body}'
                            },
                            readonly: true
                        },
                        {
                            layout: 'hbox',
                            border: false,
                            defaults: {
                                flex: 1,
                                layout: 'anchor',
                                border: false
                            },
                            items: [{
                                defaults: {
                                    anchor: '-10',
                                    readonly: true
                                },
                                items: [{
                                    xtype: 'textfield',
                                    name: 'type',
                                    fieldLabel: this.typeText,
                                    bind: {
                                        value: '{list.selection.typeText}'
                                    }
                                }]
                            },{
                                defaults: {
                                    anchor: '-10',
                                    readonly: true
                                },
                                items: [{
                                    xtype: 'textfield',
                                    name: 'channel',
                                    fieldLabel: this.channelText,
                                    bind: {
                                        value: '{list.selection.channel}'
                                    }
                                },
                                {
                                    xtype: 'textfield',
                                    name: 'role',
                                    fieldLabel: this.roleText,
                                    bind: {
                                        value: '{list.selection.role}'
                                    }
                                }]
                            },{
                                defaults: {
                                    anchor: '-0',
                                    readonly: true
                                },
                                items: [{
                                    xtype: 'textfield',
                                    name: 'user',
                                    fieldLabel: this.userText,
                                    bind: {
                                        value: '{list.selection.user}'
                                    }
                                },{
                                    xtype: 'textfield',
                                    name: 'createdAt',
                                    fieldLabel: this.createdAtText,
                                    bind: {
                                        value: '{list.selection.createdAtFormatted}'
                                    }
                                }]
                            }]
                        }
                    ]
                }]
            }
        ];
    }
});
