Ext.define('Phlexible.message.view.filter.Criteria', {
    extend: 'Ext.form.FormPanel',
    requires: [
        'Ext.ux.form.MultiSelect',
        'Ext.ux.form.IconCombo'
    ],

    xtype: 'message.filter.criteria',

    componentCls: 'p-message-filter-criteria',
    border: true,
    layout: 'border',
    bodyStyle: {
        backgroundColor: 'white'
    },

    ready: true,

    titleText: '_titleText',
    addJunctionText: '_addJunction',
    addExpressionText: '_addExpression',
    orText: '_or',
    andText: '_and',
    refreshText: '_refreshText',
    criteriaForText: '_criteriaForText',
    criteriaText: '_criteriaText',
    selectCriteriumText: '_selectCriteriumText',
    selectTypeText: '_selectTypeText',
    selectChannelText: '_selectChannelText',
    selectRoleText: '_selectRoleText',
    typeInfoText: '_typeInfoText',
    typeErrorText: '_typeErrorText',
    numberOfDaysText: '_numberOfDaysText',
    groupText: '_groupText',
    hasToMatch: '_hasToMatchText',
    orHasToMatch: '_orHasToMatchText',
    subjectLikeText: '_subjectLikeText',
    subjectNotLikeText: '_subjectNotLikeText',
    bodyLikeText: '_bodyLikeText',
    bodyNotLikeText: '_bodyNotLikeText',
    userLikeText: '_userLikeText',
    userNotLikeText: '_userNotLikeText',
    typeIsText: '_typeIsText',
    typeInText: '_typeInText',
    channelIsText: '_channelIsText',
    channelInText: '_channelInText',
    roleIsText: '_roleIsText',
    roleInText: '_roleInText',
    minAgeText: '_minAgeText',
    maxAgeText: '_maxAgeText',
    startDateText: '_startDateText',
    endDateText: '_endDateText',
    dateIsText: '_dateIsText',

    initComponent: function () {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('phlexible_api_message_get_messages'),
            success: function (response) {
                var data = Ext.decode(response.responseText);

                var typeNames = Phlexible.Config.get('message.types'),
                    types = [],
                    channels = [],
                    roles = [];

                Ext.each(data.facets.types, function(type) {
                    types.push({
                        id: type,
                        name: typeNames[type],
                        iconCls: Phlexible.message.TypeIcons[type]
                    });
                });
                Ext.each(data.facets.channels, function(channel) {
                    channels.push({
                        id: channel,
                        name: channel
                    });
                });
                Ext.each(data.facets.roles, function(role) {
                    roles.push({
                        id: role,
                        name: role
                    });
                });

                this.facets = {
                    types: types,
                    channels: channels,
                    roles: roles
                };
            },
            scope: this
        });

        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'panel',
                region: 'north',
                layout: 'form',
                border: false,
                autoScroll: false,
                padding: 5,
                items: [
                    {
                        xtype: 'textfield',
                        name: 'title',
                        fieldLabel: this.titleText,
                        flex: 1,
                        allowBlank: false,
                        bind: {
                            value: '{list.selection.title}'
                        }
                    }
                ]
            },
            {
                region: 'center',
                itemId: 'criteria',
                border: false,
                autoScroll: true,
                dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'top',
                    items: [{
                        xtype: 'segmentedbutton',
                        allowMultiple: true,
                        items: [{
                            text: this.orText,
                            pressed: true
                        },{
                            text: this.andText
                        }],
                        listeners: {
                            change: function(btn, newValue) {
                                btn.ownerCt.ownerCt.setTitle(newValue[0] === 0 ? this.orText : this.andText);
                            },
                            scope: this
                        }
                    },'->',{
                        xtype: 'button',
                        text: this.addJunctionText,
                        iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                        handler: function(btn) {
                            btn.ownerCt.ownerCt.add(this.createJunctionConfig());
                        },
                        scope: this
                    },{
                        xtype: 'button',
                        text: this.addExpressionText,
                        iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                        handler: function(btn) {
                            btn.ownerCt.ownerCt.add(this.createExpressionConfig());
                        },
                        scope: this
                    }]
                }]
            }
        ];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'top',
            items: [
                '->',
                {
                    text: this.refreshText,
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.RESET),
                    handler: this.refreshPreview,
                    scope: this
                }
            ]
        }];
    },

    /**
     * Save method to save modified data
     */
    storeFilter: function () {
        if (!this.getForm().isValid()) {
            Phlexible.Notify.failure("Validation failed");
            return;
        }

        var expression = this.createExpression();
        if (!expression) {
            Phlexible.Notify.failure("Empty expression");
            return;
        }

        this.filter.set('expression', expression);
        this.filter.set('modifiedAt', new Date());
    },

    getExpressionsPanel: function() {
        return this.getComponent('criteria');
    },

    setFilter: function(filter) {
        this.filter = filter;
        if (filter) {
            this.initExpressions(filter.get('expression'));
            this.enable();
        } else {
            this.clear();
            this.disable();
        }
    },

    clear: function () {
        this.getForm().reset();
        this.removeExpressions();
    },

    /**
     * Method to remove ALL criterias
     * Will be called before initalise a new criteria list
     */
    removeExpressions: function () {
        this.getExpressionsPanel().removeAll();
    },

    /**
     * Method to initialise the criteria field list.
     * Will be called in the loadData method
     *
     * @param {Object} expression
     */
    initExpressions: function (expression) {
        this.removeExpressions();

        Phlexible.console.info(expression);

        if (!expression || !Ext.isObject(expression) || Ext.Object.isEmpty(expression)) {
            this.addEmptyJunction(this.getExpressionsPanel());
        } else {
            Ext.each(expression.expressions, function(expression1) {
                var junctions = [];
                Ext.each(expression1.expressions, function(expression2) {
                    junctions.push(this.createExpressionConfig(expression2));
                }, this);
                this.addJunction(this.getExpressionsPanel(), expression.op, junctions);
            }, this);
        }

        this.refreshPreview();

        this.ready = true;
    },

    /**
     * Method to add a criteria to the list.
     * Will be called after clicking on the add button or during
     * the load of existing criterias (params c and v are corresponding values)
     *
     * @param {Object} expression
     */
    createExpressionConfig: function (expression) {
        var fieldConfig = this.createFieldConfig(expression || {}),
            valueConfig = this.createValueConfig(expression || {});

        return {
            xtype: 'fieldcontainer',
            border: true,
            padding: 5,
            flex: 1,
            layout: 'hbox',
            items: [
                fieldConfig,
                valueConfig,
                {
                    xtype: 'button',
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                    margin: '0 0 0 5',
                    handler: function (btn) {
                        btn.ownerCt.destroy();
                    },
                    scope: this
                }
            ]
        };
    },

    /**
     * Returns a ComboBox containing all criterias.
     * Will be used during the initialisation of a criteria panel
     * @param {Object} expression
     */
    createFieldConfig: function (expression) {
        return {
            xtype: 'combo',
            itemId: 'criterium',
            emptyText: this.criteriaText,
            margin: '0 0 0 5',
            value: expression.field + '_' + expression.op,
            store: Ext.create('Ext.data.Store', {
                fields: ['key', 'value'],
                data: [
                    ['subject_contains', this.subjectLikeText],
                    ['subject_notcontains', this.subjectNotLikeText],
                    ['body_contains', this.bodyLikeText],
                    ['body_notcontains', this.bodyNotLikeText],
                    ['user_contains', this.userLikeText],
                    ['user_notcontains', this.userNotLikeText],
                    ['type_equals', this.typeIsText],
                    //['type_in', this.typeInText],
                    ['channel_equals', this.channelIsText],
                    ['channel_in', this.channelInText],
                    ['role_equals', this.roleIsText],
                    ['role_in', this.roleInText],
                    ['date_greaterThanEqual', this.startDateText],
                    ['date_lessThanEqual', this.endDateText],
                    ['date_equals', this.dateIsText]
                ]
            }),
            listeners: {
                change: function (cb, newValue) {
                    cb.ownerCt.remove(cb.ownerCt.getComponent('value'));
                    cb.ownerCt.insert(1, this.createValueConfig({}, newValue));
                },
                scope: this
            },
            displayField: 'value',
            valueField: 'key',
            editable: false,
            allowBlank: false
        };
    },

    /**
     * Adds the corresponding field which will be get from getCorrespondingField,
     * and puts
     *
     * @param {Object} expression
     * @param {Object} identifier
     */
    createValueConfig: function (expression, identifier) {
        var field;

        if (!identifier) {
            identifier = expression.field + '_' + expression.op;
        }

        switch (identifier) {
            case 'type_equals':
                field = {
                    exprOp: 'equals',
                    exprField: 'type',
                    xtype: 'iconcombo',
                    emptyText: this.selectTypeText,
                    store: Ext.create('Ext.data.Store', {
                        data: this.facets.types,
                        fields: ['id', 'name', 'iconCls']
                    }),
                    valueField: 'id',
                    displayField: 'name',
                    iconClsField: 'iconCls',
                    allowBlank: false,
                    editable: false
                };
                break;

            case 'type_in':
                field = {
                    exprOp: 'in',
                    exprField: 'type',
                    xtype: 'tagfield',
                    emptyText: this.selectTypeText,
                    store: Ext.create('Ext.data.Store', {
                        data: this.facets.types,
                        autoLoad: true,
                        fields: ['id', 'name', 'iconCls']
                    }),
                    valueField: 'id',
                    displayField: 'name',
                    allowBlank: false,
                    editable: true,
                    stacked: true
                };
                break;

            case 'channel_equals':
                field = {
                    exprOp: 'equals',
                    exprField: 'channel',
                    xtype: 'combo',
                    emptyText: this.selectChannelText,
                    store: Ext.create('Ext.data.Store', {
                        fields: ['id', 'name'],
                        data: this.facets.channels,
                        sortInfo: {
                            field: 'name',
                            direction: 'ASC'
                        }
                    }),
                    valueField: 'id',
                    displayField: 'name',
                    allowBlank: false,
                    editable: false
                };
                break;

            case 'channel_in':
                field = {
                    exprOp: 'in',
                    exprField: 'channel',
                    xtype: 'tagfield',
                    emptyText: this.selectChannelText,
                    store: Ext.create('Ext.data.Store', {
                        fields: ['id', 'name'],
                        data: this.facets.channels
                    }),
                    allowBlank: false,
                    valueField: 'id',
                    displayField: 'name',
                    editable: true,
                    stacked: true
                };
                break;

            case 'role_equals':
                field = {
                    exprOp: 'equals',
                    exprField: 'role',
                    xtype: 'combo',
                    emptyText: this.selectRoleText,
                    store: Ext.create('Ext.data.Store', {
                        fields: ['id', 'name'],
                        data: this.facets.roles,
                        sorters: [{
                            property: 'name',
                            direction: 'ASC'
                        }]
                    }),
                    valueField: 'id',
                    displayField: 'name',
                    allowBlank: false,
                    editable: false
                };
                break;

            case 'role_in':
                field = {
                    exprOp: 'in',
                    exprField: 'role',
                    xtype: 'tagfield',
                    emptyText: this.selectRoleText,
                    store: Ext.create('Ext.data.Store', {
                        fields: ['id', 'name'],
                        data: this.facets.roles
                    }),
                    allowBlank: false,
                    valueField: 'id',
                    displayField: 'name',
                    editable: true,
                    stacked: true
                };
                break;

            case 'date_greaterThanEqual':
                field = {
                    exprOp: 'greaterThanEqual',
                    exprField: 'createdAt',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    editable: false
                };
                break;

            case 'date_lessThanEqual':
                field = {
                    exprOp: 'lessThanEqual',
                    exprField: 'createdAt',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    editable: false
                };
                break;

            case 'date_equals':
                field = {
                    exprOp: 'equals',
                    exprField: 'createdAt',
                    xtype: 'datefield',
                    format: 'Y-m-d 00:00:00',
                    editable: false
                };
                break;

            case 'subject_contains':
                field = {
                    exprOp: 'contains',
                    exprField: 'subject',
                    xtype: 'textfield'
                };
                break;

            case 'subject_notcontains':
                field = {
                    exprOp: 'containsNot',
                    exprField: 'subject',
                    xtype: 'textfield'
                };
                break;

            case 'body_contains':
                field = {
                    exprOp: 'contains',
                    exprField: 'body',
                    xtype: 'textfield'
                };
                break;

            case 'body_notcontains':
                field = {
                    exprOp: 'containsNot',
                    exprField: 'body',
                    xtype: 'textfield'
                };
                break;

            default:
                value = this.selectCriteriumText;
                field = {
                    xtype: 'displayfield'
                };
                break;
        }

        field.flex = 1;
        field.value = expression.value;
        field.name = 'value';
        field.itemId = 'value';
        field.margin = '0 0 0 5';

        return field;
    },

    refreshPreview: function () {
        this.fireEvent('refreshPreview', this.createExpression(), this.filter.get('title'));
    },

    createExpression: function (all) {
        var criteria = {op: 'or', expressions: []};

        this.getComponent('criteria').items.each(function (block) {
            var orBlock = {op: 'and', expressions: []};
            block.items.each(function (row) {
                var opField = row.getComponent('criterium'),
                    valueField;
                if (!opField.getValue()) {
                    return;
                }
                valueField = row.getComponent('value');
                if (!valueField.exprField || !valueField.exprOp) {
                    return;
                }
                orBlock.expressions.push({
                    field: valueField.exprField,
                    op: valueField.exprOp,
                    value: valueField.getValue()
                });
            }, this);
            if (orBlock.expressions.length) {
                criteria.expressions.push(orBlock);
            }
        }, this);

        if (!criteria.expressions.length) {
            criteria = null;
        }

        return criteria;
    },

    addJunction: function(panel, mode, rows) {
        var config = this.createJunctionConfig(mode);

        config.items = rows;

        return panel.add(config);
    },

    addEmptyJunction: function (panel, mode) {
        return this.addJunction(panel, mode, [this.createExpressionConfig()]);
    },

    createJunctionConfig: function (mode) {
        return {
            xtype: 'panel',
            title: this.groupText,
            //autoHeight: true,
            layout: 'vbox',
            padding: '5 5 0 5',
            tools: [{
                type: 'close',
                callback: function (p) {
                    p.destroy();
                },
                scope: this
            }],
            dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                items: [{
                    xtype: 'segmentedbutton',
                    allowMultiple: false,
                    items: [{
                        text: this.orText,
                        pressed: mode === 'or'
                    },{
                        text: this.andText,
                        pressed: mode === 'and'
                    }],
                    listeners: {
                        change: function(btn, newValue) {
                            btn.ownerCt.ownerCt.setTitle(newValue[0] === 0 ? this.orText : this.andText);
                        },
                        scope: this
                    }
                },'->',{
                    xtype: 'button',
                    text: this.addJunctionText,
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                    handler: function(btn) {
                        this.addEmptyJunction(btn.ownerCt.ownerCt, 'and');
                    },
                    scope: this
                },{
                    xtype: 'button',
                    text: this.addExpressionText,
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                    handler: function(btn) {
                        btn.ownerCt.ownerCt.add(this.createExpressionConfig());
                    },
                    scope: this
                }]
            }]
        };
    }
});
