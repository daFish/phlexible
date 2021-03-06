Ext.define('Phlexible.message.view.list.Filter', {
    extend: 'Ext.form.FormPanel',
    xtype: 'message.list.filter',

    componentCls: 'p-messages-list-filter',
    iconCls: Phlexible.Icon.get('funnel'),
    bodyPadding: 5,
    autoScroll: true,

    contentText: '_contentText',
    subjectText: '_subjectText',
    bodyText: '_bodyText',
    userText: '_userText',
    resetText: '_resetText',
    loadingText: '_loadingText',
    typeText: '_typeText',
    channelText: '_channelText',
    roleText: '_roleText',
    dateText: '_dateText',
    afterText: '_afterText',
    beforeText: '_beforeText',

    initComponent: function () {
        this.task = new Ext.util.DelayedTask(this.updateFilter, this);

        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'panel',
                title: this.contentText,
                itemId: 'text',
                layout: 'form',
                frame: true,
                collapsible: true,
                labelAlign: 'top',
                items: [
                    {
                        xtype: 'textfield',
                        fieldLabel: this.subjectText,
                        flex: 1,
                        name: 'subject',
                        labelAlign: 'top',
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
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: this.bodyText,
                        flex: 1,
                        name: 'body',
                        labelAlign: 'top',
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
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: this.userText,
                        flex: 1,
                        name: 'user',
                        labelAlign: 'top',
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
                itemId: 'types',
                title: this.typeText,
                layout: 'form',
                margin: '5 0 0 0',
                frame: true,
                collapsible: true,
                defaults: {
                    hideLabel: true
                },
                items: [{
                    plain: true,
                    frame: false,
                    html: '<div class="loading-indicator">' + this.loadingText + '...</div>'
                }]
            },
            {
                xtype: 'panel',
                itemId: 'channels',
                title: this.channelText,
                layout: 'form',
                margin: '5 0 0 0',
                frame: true,
                collapsible: true,
                defaults: {
                    hideLabel: true
                },
                items: [{
                    plain: true,
                    frame: false,
                    html: '<div class="loading-indicator">' + this.loadingText + '...</div>'
                }]
            },
            {
                xtype: 'panel',
                itemId: 'roles',
                title: this.roleText,
                layout: 'form',
                margin: '5 0 0 0',
                frame: true,
                collapsible: true,
                defaults: {
                    hideLabel: true
                },
                items: [{
                    plain: true,
                    frame: false,
                    html: '<div class="loading-indicator">' + this.loadingText + '...</div>'
                }]
            },
            {
                xtype: 'panel',
                title: this.dateText,
                layout: 'form',
                margin: '5 0 0 0',
                frame: true,
                collapsible: true,
                labelWidth: 55,
                items: [
                    {
                        xtype: 'datefield',
                        flex: 1,
                        name: 'date_after',
                        fieldLabel: this.afterText,
                        editable: false,
                        format: 'Y-m-d',
                        listeners: {
                            select: this.updateFilter,
                            scope: this
                        }
                    },
                    {
                        xtype: 'datefield',
                        flex: 1,
                        name: 'date_before',
                        fieldLabel: this.beforeText,
                        editable: false,
                        format: 'Y-m-d',
                        listeners: {
                            select: this.updateFilter,
                            scope: this
                        }
                    }
                ]
            }
        ];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            itemId: 'tbar',
            dock: 'top',
            items: [
                '->',
                {
                    text: this.resetText,
                    itemId: 'resetBtn',
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.RESET),
                    disabled: true,
                    handler: this.resetFilter,
                    scope: this
                }
            ]
        }];
    },

    updateFacets: function (facets) {
        if (!this.facetsLoaded) {
            this.loadFacets(facets);
            this.facetsLoaded = true;
            return;
        }

        if (facets.types && facets.types.length && Ext.isArray(facets.types)) {
            this.getComponent('types').items.each(function (item) {
                var found = false;
                Ext.each(facets.types, function (type) {
                    if (item.name === 'type_' + type) {
                        found = true;
                        return false;
                    }
                }, this);
                if (found) {
                    item.enable();
                } else {
                    item.disable();
                }
            }, this);
        }

        if (facets.channels && facets.channels.length && Ext.isArray(facets.channels)) {
            this.getComponent('channels').items.each(function (item) {
                var found = false;
                Ext.each(facets.channels, function (channel) {
                    if (item.name === 'channel_' + channel) {
                        found = true;
                        return false;
                    }
                }, this);
                if (found) {
                    item.enable();
                } else {
                    item.disable();
                }
            }, this);
        }

        if (facets.roles && facets.roles.length && Ext.isArray(facets.roles)) {
            this.getComponent('roles').items.each(function (item) {
                var found = false;
                Ext.each(facets.roles, function (role) {
                    if (item.name === 'role_' + role) {
                        found = true;
                        return false;
                    }
                }, this);
                if (found) {
                    item.enable();
                } else {
                    item.disable();
                }
            }, this);
        }
    },

    loadFacets: function (facets) {
        if (facets.types && facets.types.length && Ext.isArray(facets.types)) {
            var types = [];
            Ext.each(facets.types, function (item) {
                var typeName = Phlexible.message.TypeNames[item];
                types.push({
                    xtype: 'checkbox',
                    name: 'type_' + item,
                    boxLabel: '<span class="p-label p-label-message-' + typeName + '">' + typeName + '</span>',
                    listeners: {
                        change: this.updateFilter,
                        scope: this
                    }
                });
            }, this);
            this.getComponent('types').removeAll();
            this.getComponent('types').add(types);
            this.getComponent('types').show();
        } else {
            this.getComponent('types').hide();
        }

        if (facets.channels && facets.channels.length && Ext.isArray(facets.channels)) {
            var channels = [];
            Ext.each(facets.channels, function (item) {
                channels.push({
                    xtype: 'checkbox',
                    name: 'channel_' + item,
                    boxLabel: item,
                    listeners: {
                        change: this.updateFilter,
                        scope: this
                    }
                });
            }, this);
            this.getComponent('channels').removeAll();
            this.getComponent('channels').add(channels);
            this.getComponent('channels').show();
        } else {
            this.getComponent('channels').hide();
        }

        if (facets.roles && facets.roles.length && Ext.isArray(facets.roles)) {
            var roles = [];
            Ext.each(facets.roles, function (item) {
                roles.push({
                    xtype: 'checkbox',
                    name: 'role_' + item,
                    boxLabel: item,
                    listeners: {
                        change: this.updateFilter,
                        scope: this
                    }
                });
                this.getComponent('roles').removeAll();
                this.getComponent('roles').add(roles);
                this.getComponent('roles').show();
            }, this);
        } else {
            this.getComponent('roles').hide();
        }
    },

    resetFilter: function (btn) {
        this.getComponent('text').items.each(function (item) {
            item.setValue('');
        });
        this.getComponent('types').items.each(function (item) {
            item.enable();
            item.setValue(false);
        });
        this.getComponent('channels').items.each(function (item) {
            item.enable();
            item.setValue(false);
        });
        this.getComponent('roles').items.each(function (item) {
            item.enable();
            item.setValue(false);
        });
        this.updateFilter();
        btn.disable();
    },

    updateFilter: function () {
        this.getDockedComponent('tbar').getComponent('resetBtn').enable();

        var values = this.form.getValues(),
            expression = {op: 'and', expressions: []},
            types = [],
            channels = [],
            roles = [];

        Ext.Object.each(values, function(key, value) {
            if (key === 'subject' && value) {
                expression.expressions.push({field: 'subject', op: 'contains', value: value});
            } else if (key === 'body' && value) {
                expression.expressions.push({field: 'body', op: 'contains', value: value});
            } else if (key === 'user' && value) {
                expression.expressions.push({field: 'user', op: 'contains', value: value});
            } else if (key.substr(0, 5) === 'type_' && value) {
                types.push(key.substr(5));
            } else if (key.substr(0, 8) === 'channel_' && value) {
                channels.push(key.substr(8));
            } else if (key.substr(0, 5) === 'role_' && value) {
                roles.push(key.substr(5));
            }
        });

        if (types.length) {
            expression.expressions.push({field: 'type', op: 'in', value: types});
        }
        if (channels.length) {
            expression.expressions.push({field: 'channel', op: 'in', value: channels});
        }
        if (roles.length) {
            expression.expressions.push({field: 'role', op: 'in', value: roles});
        }

        this.fireEvent('updateFilter', expression);
    }
});
