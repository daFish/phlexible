/**
 * User groups edit panel
 */
Ext.define('Phlexible.user.edit.Groups', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.user-edit-groups',

    title: '_groups',
    iconCls: Phlexible.Icon.get('users'),
    border: true,
    hideMode: 'offsets',
    viewConfig: {
        stripeRows: true,
        deferEmptyText: false
    },

    key: 'groups',

    emptyText: '_empty',
    groupsText: '_groups',
    memberText: '_member',
    groupText: '_group',

    initComponent: function() {
        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.user.model.UserGroup',
            proxy: {
                type: 'ajax',
                url: Phlexible.Router.generate('phlexible_user_get_groups'),
                simpleSortMode: true,
                reader: {
                    type: 'json',
                    rootProperty: 'groups',
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
            header: this.groupText,
            sortable: true,
            dataIndex: 'group',
            width: 300,
            flex: 1
        }];

        this.callParent(arguments);
    },

    loadRecord: function(record) {
        this.getStore().getProxy().url = Phlexible.Router.generate('phlexible_user_get_user_groups', {userId: record.get('id')});
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
