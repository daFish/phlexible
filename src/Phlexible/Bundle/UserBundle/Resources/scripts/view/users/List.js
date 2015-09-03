/**
 * User grid view
 */
Ext.define('Phlexible.user.view.users.List', {
    extend: 'Ext.grid.GridPanel',
    requires: [
        'Phlexible.user.model.User',
        'Phlexible.user.window.UserWindow'
    ],
    xtype: 'user.users.list',

    cls: 'p-user-list',
    stripeRows: true,
    loadMask: true,

    selModel: {
        selType: 'rowmodel',
        mode: 'MULTI'
    },

    idText: '_id',
    usernameText: '_username',
    emailText: '_email',
    firstnameText: '_firstname',
    lastnameText: '_lastname',
    commentText: '_comment',
    expireDateText: '_expire_date',
    createDateText: '_create_date',
    createUserText: '_create_user',
    modifyDateText: '_modify_date',
    modifyUserText: '_modify_user',
    flagsText: '_flags',
    hasCommentsText: '_has_comments',
    hasExpireDateText: '_has_expire_date',
    isDisabledText: '_is_disabled',
    isExpiredDateText: '_isExpiredDateText',
    addUserText: '_add_user',
    deleteUserText: '_delete_user',
    impersonateText: '_impersonate',
    totalUsersText: '_{0}_users',
    filteredUsersText: '_{0}_users_matching_filter',
    deleteUsersWarningText: '_delete_users_warning',
    deleteUserWarningText: '_delete_user_warning',

    initComponent: function(){
        this.initMyColumns();
        this.initMyTbarItems();
        this.initMyDockedItems();
        this.initMyListeners();

        delete this.storeExtraParams;
        delete this.tbarItems;

        this.callParent(arguments);
    },

    initMyColumns: function() {
        this.columns = [{
            header: this.idText,
            sortable: true,
            dataIndex: 'id',
            hidden: true,
            width: 80
        },{
            header: this.firstnameText,
            sortable: true,
            dataIndex: 'firstname',
            width: 150
        },{
            header: this.lastnameText,
            sortable: true,
            dataIndex: 'lastname',
            width: 150
        },{
            header: this.usernameText,
            sortable: true,
            dataIndex: 'username',
            width: 150
        },{
            header: this.emailText,
            sortable: true,
            dataIndex: 'email',
            width: 250
        },{
            header: this.commentText,
            sortable: true,
            dataIndex: 'comment',
            width: 200,
            hidden: true
        },{
            header: this.expireDateText,
            sortable: true,
            dataIndex: 'expireDate',
            hidden: true,
            width: 100
        },{
            header: this.createDateText,
            sortable: true,
            dataIndex: 'createDate',
            hidden: true,
            width: 100
        },{
            header: this.createUserText,
            sortable: true,
            dataIndex: 'createUser',
            hidden: true,
            width: 100
        },{
            header: this.modifyDateText,
            sortable: true,
            dataIndex: 'modifyDate',
            hidden: true,
            width: 100
        },{
            header: this.modifyUserText,
            sortable: true,
            dataIndex: 'modifyUser',
            hidden: true,
            width: 100
        },{
            header: this.flagsText,
            sortable: false,
            width: 80,
            renderer: this.flagsRenderer
        }, {
            xtype: 'actioncolumn',
            width: 30,
            items: [{
                iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                tooltip: this.deleteText,
                handler: function (grid, rowIndex, colIndex, item, e, user) {
                    if (user) {
                        this.store.remove(user);
                    }
                },
                scope: this
            }]
        }];
    },

    initMyTbarItems: function() {
        this.tbarItems = [];

        if (Phlexible.User.isGranted('ROLE_USER_ADMIN_CREATE')) {
            this.tbarItems.push({
                itemId: 'addBtn',
                text: this.addUserText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                handler: this.addUser,
                scope: this
            });
        }

        if (Phlexible.User.isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            if (this.tbarItems.length) {
                this.tbarItems.push('-');
            }
            this.tbarItems.push({
                itemId: 'impersonateBtn',
                text: this.impersonateText,
                iconCls: Phlexible.Icon.get('user-thief'),
                handler: this.impersonateUser,
                scope: this,
                disabled: true
            });
        }

        /*
        // TODO: enable on buffered paging
        this.tbarItems.push('->');
        this.tbarItems.push({
            xtype: 'tbtext',
            itemId: 'countBtn',
            text: Ext.String.format(this.totalUsersText, '-')
        });
        */
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'top',
            itemId: 'tbar',
            items: this.tbarItems
        },{
            // TODO: remove if buffered paging works
            xtype: 'pagingtoolbar',
            itemId: 'pager',
            dock: 'bottom',
            displayInfo: true,
            store: this.store
        }];
    },

    initMyListeners: function() {
        this.on({
            selectionchange: function(view, records) {
                var tb = this.getDockedComponent('tbar');
                if (records.length === 0) {
                    tb.items.each(function(item) {
                        item.disable();
                    });
                    tb.items.items[0].enable();
                } else if (records.length === 1) {
                    tb.items.each(function(item) {
                        item.enable();
                    });
                    tb.items.items[0].enable();
                } else {
                    tb.items.each(function(item) {
                        item.disable();
                    });
                    tb.getComponent(0).enable();
                    tb.getComponent(1).enable();
                }
            },
            rowdblclick: function(view, user){
                if (!Phlexible.User.isGranted('ROLE_USER_ADMIN_UPDATE')) {
                    return;
                }

                this.editUser(user);
            },
            scope: this
        });

    },

    addUser: function(){
        var user = Ext.create('Phlexible.user.model.User', {
                id: '',
                username: '',
                firstname: '',
                lastname: '',
                email: '',
                options: {
                    theme: Phlexible.Config.get('users.defaults.theme')
                },
                account: {
                    forcePasswordChange: Phlexible.Config.get('users.defaults.force_password_change'),
                    noPasswordChange: Phlexible.Config.get('users.defaults.cant_change_password'),
                    noPasswordExpire: Phlexible.Config.get('users.defaults.password_doesnt_expire')
                },
                roles: [
                    'user'
                ]
            }),
            win = Ext.create('Phlexible.user.window.UserWindow', {
                mode: 'add',
                user: user,
                listeners: {
                    create: function(user) {
                        this.store.add(user);

                        this.store.sync();
                    },
                    scope: this
                }
            });

        win.show();
    },

    editUser: function(user){
        var w = Ext.create('Phlexible.user.window.UserWindow', {
            mode: 'edit',
            user: user,
            listeners: {
                update: function(user){
                    this.store.sync();
                },
                scope: this
            }
        });
        w.show();
    },

    deleteUser: function() {
        var selectionModel = this.getSelectionModel(),
            users = selectionModel.getSelection(),
            msg;

        if (users.length > 1) {
            msg = this.deleteUsersWarningText;
        } else if (users.length === 1) {
            msg = Ext.String.format(this.deleteUserWarningText, users[0].get('username'));
        } else {
            return;
        }

        Ext.MessageBox.confirm(this.deleteUserText, msg, function(btn) {
            if (btn !== 'yes') {
                return;
            }
            Ext.each(users, function(user) {
                user.drop();
                return;
                Ext.Ajax.request({
                    url: Phlexible.Router.generate('phlexible_api_user_delete_user', {userId: userId}),
                    method: 'DELETE',
                    success: function(response){
                        deletedIds.push(userId);
                        if (response.status !== 204) {
                            Phlexible.Notify.failure('Deleted user failed.');
                        }
                        if (deletedIds.length === userIds.length) {
                            this.getStore().load();
                        }
                    },
                    failure: function() {
                        deletedIds.push(userId);
                        Phlexible.Notify.failure('Deleted user failed.');
                        if (deletedIds.length === userIds.length) {
                            this.getStore().load();
                        }
                    },
                    scope:this
                });
            }, this);
            this.getStore().sync();
        }, this);
    },

    impersonateUser: function() {
        var selectionModel = this.getSelectionModel(),
            record = selectionModel.getSelection()[0];

        document.location.href = Phlexible.Router.generate('phlexible_gui', {"_switch_user": record.get('username')});
    },

    flagsRenderer: function(v, md, r) {
        v = '';
        if (r.get('comment')) {
            v += Phlexible.Icon.inline('sticky-note-text', {'data-qtip': this.hasCommentsText});
        }
        if (!r.get('enabled')) {
            v += Phlexible.Icon.inline('key', {'data-qtip': this.isDisabledText});
        }
        if (r.get('expired')) {
            v += Phlexible.Icon.inline('alarm-clock--exclamation', {'data-qtip': this.isExpiredDateText});
        }
        if (r.get('expiresAt')) {
            v += Phlexible.Icon.inline('alarm-clock-select', {'data-qtip': this.hasExpireDateText});
        }
        return v;
    }
});
