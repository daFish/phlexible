/**
 * User roles edit panel
 */
Ext.define('Phlexible.user.edit.Roles', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.user-edit-roles',

    title: '_roles',
    iconCls: Phlexible.Icon.get('user-business'),
    border: true,
    hideMode: 'offsets',
    viewConfig: {
        stripeRows: true,
        deferEmptyText: false
    },

    key: 'roles',

    emptyText: '_empty',
    rolesText: '_roles',
    memberText: '_member',
    roleText: '_role',

    initComponent: function() {
        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.user.model.UserRole',
            proxy: {
                type: 'ajax',
                url: Phlexible.Router.generate('phlexible_user_get_roles'),
                simpleSortMode: true,
                reader: {
                    type: 'json',
                    rootProperty: 'roles',
                    idProperty: 'id',
                    totalProperty: 'count'
                },
                extraParams: {
                    id: null
                }
            },
            sorters: [{
                property: 'role',
                direction: 'ASC'
            }],
            autoLoad: true
        });

        this.columns = [{
            xtype: 'checkcolumn',
            text: this.memberText,
            dataIndex: 'member',
            width: 55
        },{
            header: this.roleText,
            sortable: true,
            dataIndex: 'role',
            width: 300,
            flex: 1
        }];

        this.callParent(arguments);
    },

    loadRecord: function(record) {
        this.getStore().getProxy().url = Phlexible.Router.generate('phlexible_user_get_user_roles', {userId: record.get('id')});
        this.getStore().load();
    },

    isValid: function() {
        return true;
    },

    getValues: function() {
        var mr = this.getStore().getRange();
        var data = {};
        for(var i=0; i<mr.length; i++) {
            data[mr[i].get('id')] = mr[i].get('member') ? 1 : 0;
        }
        return data;
    }
});
