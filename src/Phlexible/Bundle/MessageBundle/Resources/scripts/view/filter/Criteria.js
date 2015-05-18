Ext.define('Phlexible.message.view.filter.Criteria', {
    extend: 'Ext.form.FormPanel',
    requires: [
        'Ext.ux.form.MultiSelect',
        'Ext.ux.form.IconCombo'
    ],

    xtype: 'message.filter.criteria',

    cls: 'p-message-filter-criteria',
    border: true,
    layout: 'border',
    bodyStyle: {
        backgroundColor: 'white'
    },

    ready: true,

    titleText: '_titleText',
    saveText: '_saveText',
    addOrBlockText: '_addOrBlockText',
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
                        allowBlank: false
                    }
                ]
            },
            {
                region: 'center',
                itemId: 'criteria',
                border: false,
                autoScroll: true
            }
        ];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'top',
            items: [
                {
                    text: this.saveText,
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.SAVE),
                    handler: this.save,
                    scope: this
                },
                '-',
                {
                    text: this.addOrBlockText,
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                    handler: this.addEmptyOrBlock,
                    scope: this
                },
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

    getCriteriaPanel: function() {
        return this.getComponent('criteria');
    },

    /**
     * Save method to save modified data
     */
    save: function () {
        if (!this.getForm().isValid()) {
            Phlexible.Notify.failure("Validation failed");
            return;
        }

        var expression = this.createExpression();
        if(!expression) {
            Phlexible.Notify.failure("Empty expression");
            return;
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('phlexible_api_message_put_filter', {filterId: this.record.id}),
            jsonData: {
                title: this.getComponent(0).getComponent(0).getValue(),
                expression: expression
            },
            success: this.saveSuccess,
            failure: this.saveFailure,
            scope: this
        });
    },

    saveSuccess: function (response) {
        var data = Ext.decode(response.responseText);

        if (data.success) {
            Phlexible.Notify.success(data.msg);
            this.fireEvent('reload');
        } else {
            Phlexible.Notify.failure(data.msg);
        }
    },

    saveFailure: function () {
    },

    /**
     * Method to load data to the Information form and then to load the criteria list
     * @param {Object} record
     */
    loadData: function (record) {
        this.record = record;
        this.enable();
        this.getForm().loadRecord(record);

        this.setTitle(Ext.String.format(this.criteriaForText, record.data.title));

        // Loads the criteria list
        this.initCriteriaFields(record.data.criteria);
    },

    clear: function () {
        this.getForm().reset();
        this.removeCriteriaFields();
        this.setTitle(this.criteriaText);
    },

    /**
     * Method to remove ALL criterias
     * Will be called before initalise a new criteria list
     */
    removeCriteriaFields: function () {
        this.getCriteriaPanel().removeAll();
    },

    /**
     * Method to initialise the criteria field list.
     * Will be called in the loadData method
     *
     * @param {Array} blocks
     */
    initCriteriaFields: function (blocks) {
        this.removeCriteriaFields();

        console.info(blocks);

        if (!blocks || !Ext.isArray(blocks) || !blocks.length) {
            this.addEmptyOrBlock();
        } else {
            Ext.each(blocks, function(block) {
                var rows = [];
                Ext.each(block.group, function(row) {
                    rows.push(this.createRowConfig(row.criteria, row.value));
                });
                this.addOrBlock(rows);
            });
        }

        this.refreshPreview();

        this.ready = true;
    },

    /**
     * Method to add a criteria to the list.
     * Will be called after clicking on the add button or during
     * the load of existing criterias (params c and v are corresponding values)
     *
     * @param {String} criterium
     * @param {String} value
     */
    createRowConfig: function (criterium, value) {
        var criteriaConfig = this.createCriteriumConfig(criterium),
            valueConfig = this.createValueConfig(criterium, value);

        return {
            xtype: 'fieldcontainer',
            border: true,
            padding: 5,
            flex: 1,
            layout: 'hbox',
            items: [
                criteriaConfig,
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
     * @param {String} value
     */
    createCriteriumConfig: function (value) {
        return {
            xtype: 'combo',
            itemId: 'criterium',
            emptyText: this.criteriaText,
            width: 150,
            value: value,
            margin: '0 0 0 5',
            store: Ext.create('Ext.data.Store', {
                fields: ['key', 'value'],
                data: [
                    ['subjectLike', this.subjectLikeText],
                    ['subjectNotLike', this.subjectNotLikeText],
                    ['bodyLike', this.bodyLikeText],
                    ['bodyNotLike', this.bodyNotLikeText],
                    ['userLike', this.userLikeText],
                    ['userNotLike', this.userNotLikeText],
                    ['typeIs', this.typeIsText],
                    //['typeIn', this.typeInText],
                    ['channelIs', this.channelIsText],
                    ['channelIn', this.channelInText],
                    ['roleIs', this.roleIsText],
                    ['roleIn', this.roleInText],
                    ['minAge', this.minAgeText],
                    ['maxAge', this.maxAgeText],
                    ['startDate', this.startDateText],
                    ['endDate', this.endDateText],
                    ['dateIs', this.dateIsText]
                ]
            }),
            listeners: {
                change: function (cb, newValue) {
                    cb.ownerCt.remove(cb.ownerCt.getComponent('value'));
                    cb.ownerCt.insert(1, this.createValueConfig(newValue));
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
     * @param {String} criterium
     * @param {String} value
     */
    createValueConfig: function (criterium, value) {
        var field;

        switch (criterium) {
            case 'typeIs':
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

            case 'typeIn':
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

            case 'channelIs':
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

            case 'channelIn':
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

            case 'roleIs':
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

            case 'roleIn':
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

            case 'minAge':
                field = {
                    xtype: 'numberfield',
                    emptyText: this.numberOfDaysText
                };
                break;

            case 'maxAge':
                field = {
                    xtype: 'numberfield',
                    emptyText: this.numberOfDaysText
                };
                break;

            case 'startDate':
                field = {
                    exprOp: 'greaterThanEqual',
                    exprField: 'createdAt',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    editable: false
                };
                break;

            case 'endDate':
                field = {
                    exprOp: 'lessThanEqual',
                    exprField: 'createdAt',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    editable: false
                };
                break;

            case 'dateIs':
                field = {
                    exprOp: 'equals',
                    exprField: 'createdAt',
                    xtype: 'datefield',
                    format: 'Y-m-d 00:00:00',
                    editable: false
                };
                break;

            case 'subjectLike':
                field = {
                    exprOp: 'contains',
                    exprField: 'subject',
                    xtype: 'textfield'
                };
                break;

            case 'subjectNotLike':
                field = {
                    exprOp: 'containsNot',
                    exprField: 'subject',
                    xtype: 'textfield'
                };
                break;

            case 'bodyLike':
                field = {
                    exprOp: 'contains',
                    exprField: 'body',
                    xtype: 'textfield'
                };
                break;

            case 'bodyNotLike':
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
        field.width = 330;
        field.value = value;
        field.name = 'value';
        field.itemId = 'value';
        field.margin = '0 0 0 5';

        return field;
    },

    refreshPreview: function () {
        this.fireEvent('refreshPreview', this.createExpression(), this.record.data.title);
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

        console.info('serialize:', criteria);

        return criteria;
    },

    addOrBlock: function(rows) {
        var config = this.createBlockConfig(rows);

        config.items = rows;

        return this.getCriteriaPanel().add(config);
    },

    addEmptyOrBlock: function () {
        return this.addOrBlock([this.createRowConfig()]);
    },

    createBlockConfig: function () {
        return {
            xtype: 'panel',
            title: this.groupText,
            //autoHeight: true,
            layout: 'vbox',
            padding: '5 5 0 5',
            tools: [{
                type: 'plus',
                callback: function (p) {
                    p.add(this.createRowConfig());
                },
                scope: this
            },{
                type: 'close',
                callback: function (p) {
                    p.destroy();
                },
                scope: this
            }]
        };
    }
});
