/**
 * User groups edit panel
 */
Ext.define('Phlexible.user.edit.Groups', {
    extend: 'Ext.grid.Panel',
    xtype: 'user.edit-groups',

    iconCls: Phlexible.Icon.get('users'),
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
                url: Phlexible.Router.generate('phlexible_api_user_get_groups'),
                simpleSortMode: true,
                reader: {
                    type: 'json',
                    rootProperty: 'groups',
                    totalProperty: 'count'
                }
            },
            sorters: [{
                property: 'role',
                direction: 'ASC'
            }],
            autoLoad: this.mode !== 'edit'
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

    loadUser: function(user) {
        if (this.mode === 'edit') {
            this.getStore().getProxy().url = Phlexible.Router.generate('phlexible_api_user_get_user_groups', {userId: user.get('id')});
            this.getStore().load();
        }
    },

    isValid: function() {
        return true;
    },

    applyToUser: function(user) {
        var groups = {};
        this.getStore().each(function(group) {
            if (!group.get('member')) {
                return;
            }
            groups.push(group.get('id'));
        });

        user.set('groups', groups);
    }
});
