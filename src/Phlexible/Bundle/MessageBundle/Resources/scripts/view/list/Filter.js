Ext.define('Phlexible.message.view.list.Filter', {
    extend: 'Ext.form.FormPanel',
    xtype: 'message.list.filter',

    cls: 'p-messages-list-filter',
    iconCls: 'p-message-filter-icon',
    bodyPadding: 5,
    autoScroll: true,

    subjectText: '_subjectText',
    bodyText: '_bodyText',
    userText: '_userText',
    resetText: '_resetText',
    priorityText: '_priorityText',
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

        Ext.Ajax.request({
            url: Phlexible.Router.generate('messages_messages_facets'),
            success: function (response) {
                var facets = Ext.decode(response.responseText);

                this.loadFacets(facets);
            },
            scope: this
        });

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'panel',
                title: this.textText,
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
                itemId: 'priorities',
                title: this.priorityText,
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
        if (facets.priorities && facets.priorities.length && Ext.isArray(facets.priorities)) {
            this.getComponent('priorities').items.each(function (item) {
                var found = false;
                Ext.each(facets.priorities, function (priority) {
                    if (item.name === 'priority_' + priority) {
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
        if (facets.priorities && facets.priorities.length && Ext.isArray(facets.priorities)) {
            var priorities = [];
            Ext.each(facets.priorities, function (item) {
                priorities.push({
                    xtype: 'checkbox',
                    name: 'priority_' + item.id,
                    boxLabel: Phlexible.Icon.inlineText(Phlexible.message.PriorityIcons[item.title], item.title),
                    listeners: {
                        change: this.updateFilter,
                        scope: this
                    }
                });
            }, this);
            this.getComponent('priorities').removeAll();
            this.getComponent('priorities').add(priorities);
            this.getComponent('priorities').show();
        } else {
            this.getComponent('priorities').hide();
        }

        if (facets.types && facets.types.length && Ext.isArray(facets.types)) {
            var types = [];
            Ext.each(facets.types, function (item) {
                types.push({
                    xtype: 'checkbox',
                    name: 'type_' + item.id,
                    boxLabel: Phlexible.Icon.inlineText(Phlexible.message.TypeIcons[item.title], item.title),
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
                    name: 'channel_' + item.id,
                    boxLabel: item.title,
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
                    name: 'role_' + item.id,
                    boxLabel: item.title,
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

        this.doLayout();
    },

    resetFilter: function (btn) {
        this.getComponent('text').items.each(function (item) {
            item.setValue('');
        });
        this.getComponent('priorities').items.each(function (item) {
            item.enable();
            item.setValue(false);
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
            filter = {mode: 'AND', value: []},
            priorities = [],
            types = [],
            channels = [],
            roles = [];

        Ext.Object.each(values, function(key, value) {
            if (key === 'subject' && value) {
                filter.value.push({type: 'subjectLike', value: value})
            } else if (key === 'body' && value) {
                filter.value.push({type: 'bodyLike', value: value})
            } else if (key === 'user' && value) {
                filter.value.push({type: 'userLike', value: value})
            } else if (key.substr(0, 9) === 'priority_' && value) {
                priorities.push(key.substr(9));
            } else if (key.substr(0, 5) === 'type_' && value) {
                types.push(key.substr(5));
            } else if (key.substr(0, 8) === 'channel_' && value) {
                channels.push(key.substr(8));
            } else if (key.substr(0, 5) === 'role_' && value) {
                roles.push(key.substr(5));
            }
        });

        if (priorities.length) {
            filter.value.push({type: 'priorityIn', value: priorities});
        }
        if (types.length) {
            filter.value.push({type: 'typeIn', value: types});
        }
        if (channels.length) {
            filter.value.push({type: 'channelIn', value: channels});
        }
        if (roles.length) {
            filter.value.push({type: 'rolesIn', value: roles});
        }

        this.fireEvent('updateFilter', filter);
    }
});
