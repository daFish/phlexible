Ext.define('Phlexible.message.view.filter.CriteriaForm', {
    extend: 'Ext.form.FormPanel',
    alias: 'widget.message-filter-criteria',
    requires: ['Ext.ux.form.MultiSelect'],

    title: '_CriteriaForm',
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
    selectPriorityText: '_selectPriorityText',
    selectTypeText: '_selectTypeText',
    selectChannelText: '_selectChannelText',
    selectRoleText: '_selectRoleText',
    priorityUrgentText: '_priorityUrgent',
    priorityHighText: '_priorityHighText',
    priorityNormalText: '_priorityNormalText',
    priorityLowText: '_priorityLowText',
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
    priorityIsText: '_priorityIsText',
    priorityInText: '_priorityInText',
    priorityMinText: '_priorityMinText',
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
            url: Phlexible.Router.generate('messages_filter_facets'),
            success: function (response) {
                var data = Ext.decode(response.responseText);

                var priorities = [],
                    types = [],
                    channels = [],
                    roles = [];

                Ext.each(data.priorities, function(priority) {
                    priorities.push({
                        id: priority,
                        name: data.priorityNames[priority],
                        iconCls: Phlexible.message.PriorityIcons[priority]
                    });
                });
                Ext.each(data.types, function(type) {
                    types.push({
                        id: type,
                        name: data.typeNames[type],
                        iconCls: Phlexible.message.TypeIcons[type]
                    });
                });
                Ext.each(data.channels, function(channel) {
                    channels.push({
                        id: channel,
                        name: channel
                    });
                });
                Ext.each(data.roles, function(role) {
                    roles.push({
                        id: role,
                        name: role
                    });
                });

                this.facets = {
                    priorities: priorities,
                    types: types,
                    channels: channels,
                    roles: roles
                };
                console.log(this.facets);
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
            Phlexible.Notify.failure("Invalid Form");
            return;
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('messages_filter_update', {id: this.record.id}),
            params: {
                title: this.getComponent(0).getComponent(0).getValue(),
                criteria: Ext.encode(this.serializeCriteria())
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
                    ['priorityIs', this.priorityIsText],
                    ['priorityIn', this.priorityInText],
                    ['priorityMin', this.priorityMinText],
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
            case 'priorityIs':
                field = {
                    xtype: 'iconcombo',
                    emptyText: this.selectPriorityText,
                    store: Ext.create('Ext.data.Store', {
                        data: this.facets.priorities,
                        fields: ['id', 'name', 'iconCls']
                    }),
                    valueField: 'id',
                    displayField: 'name',
                    iconClsField: 'iconCls',
                    allowBlank: false,
                    editable: false
                };
                break;

            case 'priorityIn':
                field = {
                    xtype: 'tagfield',
                    emptyText: this.selectPriorityText,
                    store: Ext.create('Ext.data.Store', {
                        data: this.facets.priorities,
                        fields: ['id', 'name', 'iconCls']
                    }),
                    valueField: 'id',
                    displayField: 'name',
                    allowBlank: false,
                    editable: true,
                    stacked: true
                };
                break;

            case 'priorityMin':
                field = {
                    xtype: 'iconcombo',
                    emptyText: this.selectPriorityText,
                    store: Ext.create('Ext.data.Store', {
                        data: this.facets.priorities,
                        fields: ['id', 'name', 'iconCls']
                    }),
                    valueField: 'id',
                    displayField: 'name',
                    iconClsField: 'iconCls',
                    allowBlank: false,
                    editable: false
                };
                break;

            case 'typeIs':
                field = {
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
            case 'maxAge':
                field = {
                    xtype: 'numberfield',
                    emptyText: this.numberOfDaysText
                };
                break;

            case 'startDate':
            case 'endDate':
                field = {
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    editable: false
                };
                break;

            case 'dateIs':
                field = {
                    xtype: 'datefield',
                    format: 'Y-m-d 00:00:00',
                    editable: false
                };
                break;

            case 'subjectLike':
            case 'subjectNotLike':
            case 'bodyLike':
            case 'bodyNotLike':
                field = {
                    xtype: 'textfield'
                };
                break;

            default:
                value = this.selectCriteriumText;
                field = {
                    xtype: 'displayfield'
                }
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
        this.fireEvent('refreshPreview', this.serializeCriteria(), this.record.data.title);
    },

    serializeCriteria: function (all) {
        var criteria = {mode: 'OR', type: 'collection', value: []};

        this.getComponent('criteria').items.each(function (block) {
            var orBlock = {mode: 'AND', type: 'collection', value: []};
            block.items.each(function (row) {
                if (!row.getComponent('criterium').getValue()) {
                    return;
                }
                orBlock.value.push({
                    type: row.getComponent('criterium').getValue(),
                    value: row.getComponent('value').getValue()
                });
            }, this);
            if (orBlock.value.length) {
                criteria.value.push(orBlock);
            }
        }, this);

        if (!criteria.value.length) {
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
