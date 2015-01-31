/**
 * User filter view
 */
Ext.define('Phlexible.user.view.users.Filter', {
    extend: 'Ext.form.FormPanel',
    requires: ['Ext.ux.form.trigger.Clear'],

    xtype: 'user.users.filter',

    cls: 'p-user-filter',
    iconCls: Phlexible.Icon.get(Phlexible.Icon.FILTER),
    bodyPadding: 5,
    defaultType: 'textfield',
    autoScroll: true,

    fieldDefaults: {
        labelWidth: 60
    },

    searchText: '_search',
    accountText: '_account',
    isDisabledText: '_is_disabled',
    hasExpireDateText: '_has_expire',
    isExpiredText: '_is_expired',
    resetText: '_reset',
    rolesText: '_roles',
    groupsText: '_groups',

    /**
     * @event applySearch
     */

    /**
     * @event applySearch
     */

    /**
     *
     */
    initComponent: function() {
        this.initMyTask();
        this.initMyItems();
        this.initMyDockedItems();
        this.initMyListeners();

        this.callParent(arguments);
    },

    initMyTask: function() {
        this.task = new Ext.util.DelayedTask(this.updateFilter, this);
    },

    initMyItems: function() {
        this.items = [{
            xtype: 'panel',
            title: this.searchText,
            layout: 'form',
            frame: true,
            collapsible: true,
            items: [{
                xtype: 'textfield',
                name: 'key',
                value: this.filterHelper.get('key'),
                hideLabel: true,
                flex: 1,
                triggers: {
                    clear: {
                        type: 'clear'
                    }
                },
                enableKeyEvents: true,
                listeners: {
                    change: function(field, newValue, oldValue) {
                        if (oldValue && !newValue) {
                            this.task.cancel();
                            this.updateFilter();
                        }
                    },
                    keyup: function(field, event) {
                        if(event.getKey() === event.ENTER){
                            this.task.cancel();
                            this.updateFilter();
                            return;
                        }

                        this.task.delay(500);
                    },
                    scope: this
                }
            }]
        },{
            xtype: 'panel',
            title: this.accountText,
            iconCls: Phlexible.Icon.get('key'),
            layout: 'form',
            margin: '5 0 0 0',
            frame: true,
            collapsible: true,
            items: [{
                xtype: 'checkbox',
                name: 'account_disabled',
                checked: this.filterHelper.get('account_disabled'),
                hideLabel: true,
                boxLabel: this.isDisabledText,
                listeners: {
                    change: this.updateFilter,
                    scope: this
                }
            },{
                xtype: 'checkbox',
                name: 'account_has_expire_date',
                checked: this.filterHelper.get('account_has_expire_date'),
                hideLabel: true,
                boxLabel: this.hasExpireDateText,
                listeners: {
                    change: this.updateFilter,
                    scope: this
                }
            },{
                xtype: 'checkbox',
                name: 'account_expired',
                checked: this.filterHelper.get('account_expired'),
                hideLabel: true,
                boxLabel: this.isExpiredText,
                listeners: {
                    change: this.updateFilter,
                    scope: this
                }
            }]
        },{
            xtype: 'panel',
            title: this.rolesText,
            iconCls: Phlexible.Icon.get('user-business'),
            layout: 'form',
            margin: '5 0 0 0',
            frame: true,
            collapsible: true,
            items: [{
                xtype: 'tagfield',
                name: 'roles',
                value: this.filterHelper.get('roles'),
                hideLabel: true,
                flex: 1,
                displayField: 'name',
                valueField: 'id',
                stacked: true,
                forceSelection: true,
                store: Ext.create('Ext.data.Store', {
                    model: 'Phlexible.user.model.UserRole',
                    proxy: {
                        type: 'ajax',
                        url: Phlexible.Router.generate('phlexible_user_get_roles'),
                        simpleSortMode: true,
                        reader: {
                            type: 'json',
                            idProperty: 'id'
                        }
                    },
                    sorters: [{
                        property: 'role',
                        direction: 'ASC'
                    }]
                }),
                listeners: {
                    change: this.updateFilter,
                    scope: this
                }
            }]
        },{
            xtype: 'panel',
            title: this.groupsText,
            iconCls: Phlexible.Icon.get('users'),
            layout: 'form',
            margin: '5 0 0 0',
            frame: true,
            collapsible: true,
            items: [{
                xtype: 'tagfield',
                name: 'groups',
                value: this.filterHelper.get('groups'),
                hideLabel: true,
                flex: 1,
                displayField: 'group',
                valueField: 'id',
                stacked: true,
                forceSelection: true,
                store: Ext.create('Ext.data.Store', {
                    model: 'Phlexible.user.model.UserGroup',
                    proxy: {
                        type: 'ajax',
                        url: Phlexible.Router.generate('phlexible_user_get_groups'),
                        simpleSortMode: true,
                        reader: {
                            type: 'json',
                            rootProperty: 'groups',
                            idProperty: 'id'
                        }
                    },
                    sorters: [{
                        property: 'role',
                        direction: 'ASC'
                    }]
                }),
                listeners: {
                    change: this.updateFilter,
                    scope: this
                }
            }]
        }];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'bottom',
            itemId: 'buttons',
            ui: 'footer',
            items: [
                '->',
            {
                text: this.resetText,
                itemId: 'resetBtn',
                iconCls: Phlexible.Icon.get(Phlexible.Icon.RESET),
                handler: this.resetFilter,
                scope: this,
                disabled: !this.filterHelper.hasValue()
            }]
        }];
    },

    initMyListeners: function() {
        this.on({
            render: function(){
                var keyMap = this.getKeyMap();
                keyMap.addBinding({
                    key: Ext.EventObject.ENTER,
                    fn: this.updateFilter,
                    scope: this
                });
            },
            scope: this
        });
    },

    resetFilter: function() {
        this.isUpdating = true;

        this.form.setValues(this.filterHelper.getResetValues());

        this.isUpdating = false;

        this.updateFilter();
    },

    updateFilter: function() {
        if (this.isUpdating) {
            return;
        }

        var values = this.form.getValues() || {};
        this.filterHelper.applyValues(values);

        this.fireEvent('applyFilter', values, this.filterHelper);

        this.getDockedComponent('buttons').getComponent('resetBtn').setDisabled(!this.filterHelper.hasValue());
    }
});
