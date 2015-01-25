Ext.define('Phlexible.message.view.list.FilterPanel', {
    extend: 'Ext.form.FormPanel',
    alias: 'widget.message-list-filter',

    bodyPadding: 5,
    cls: 'p-messages-view-filter',
    iconCls: 'p-message-filter-icon',
    autoScroll: true,

    textText: '_textText',
    subjectText: '_subjectText',
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
                        fieldLabel: this.textText,
                        flex: 1,
                        name: 'text',
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
            filter = {mode: 'AND', criteria: []};

        if (values.subject) {
            filter.criteria.push({type: 'subjectLike', value: values.subject})
        }
        if (values.body) {
            filter.criteria.push({type: 'subjectLike', value: values.body})
        }

        this.fireEvent('updateFilter', filter);
    }
});
