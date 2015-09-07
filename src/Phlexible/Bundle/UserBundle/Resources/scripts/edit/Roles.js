/**
 * User roles edit panel
 */
Ext.define('Phlexible.user.edit.Roles', {
    extend: 'Ext.grid.Panel',
    xtype: 'user.edit-roles',

    iconCls: Phlexible.Icon.get('user-business'),
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
                url: Phlexible.Router.generate('phlexible_api_user_get_roles'),
                simpleSortMode: true,
                reader: {
                    type: 'json',
                    rootProperty: 'roles',
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
            header: this.roleText,
            sortable: true,
            dataIndex: 'role',
            width: 300,
            flex: 1
        }];

        this.callParent(arguments);
    },

    loadUser: function(user) {
        if (this.mode === 'edit') {
            this.getStore().getProxy().setUrl(Phlexible.Router.generate('phlexible_api_user_get_user_roles', {userId: user.get('id')}));
            this.getStore().load();
        }
    },

    isValid: function() {
        return true;
    },

    applyToUser: function(user) {
        var roles = {};
        this.getStore().each(function(role) {
            if (!role.get('member')) {
                return;
            }
            roles.push(role.get('id'));
        });

        user.set('roles', roles);
    }
});
