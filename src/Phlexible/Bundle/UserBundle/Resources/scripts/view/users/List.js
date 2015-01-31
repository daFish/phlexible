/**
 * User grid view
 */
Ext.define('Phlexible.user.view.users.List', {
    extend: 'Ext.grid.GridPanel',

    xtype: 'user.users.list',

    cls: 'p-user-list',
    stripeRows: true,
    loadMask: true,

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
    addUserText: '_add_user',
    deleteUserText: '_delete_user',
    impersonateText: '_impersonate',
    totalUsersText: '_{0}_users',
    filteredUsersText: '_{0}_users_matching_filter',
    deleteUsersWarningText: '_delete_users_warning',
    deleteUserWarningText: '_delete_user_warning',

    initComponent: function(){
        this.initMyStoreExtraParams();
        this.initMyStore();
        this.initMyColumns();
        this.initMySelModel();
        this.initMyTbarItems();
        this.initMyDockedItems();
        this.initMyListeners();

        delete this.storeExtraParams;
        delete this.tbarItems;

        this.callParent(arguments);
    },

    initMyStoreExtraParams: function() {
        this.storeExtraParams = {};
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.user.model.User',
            proxy: {
                type: 'ajax',
                url: Phlexible.Router.generate('phlexible_user_get_users'),
                simpleSortMode: true,
                reader: {
                    type: 'json',
                    rootProperty: 'users',
                    idProperty: 'id',
                    totalProperty: 'count'
                },
                extraParams: this.storeExtraParams
            },
            // TODO: enable when buffered paging reload works. disabled for now.
            buffered: false, //true,
            autoLoad: true,
            pageSize: 100,
            leadingBufferZone: 200,
            remoteSort: true,
            sorters: [{
                property: 'username',
                direction: 'ASC'
            }],
            listeners: {
                beforeload: function(store) {
                    store.getProxy().extraParams.search = Ext.encode(this.filterHelper.getSetValues());

                    // TODO: workaround due to extjs-4.2.1 buffered store load bug
                    this.getSelectionModel().deselectAll();
                },
                load: function() {
                    this.fireEvent('storeReload', this, this.store);
                },
                /*
                // TODO: enable on buffered paging
                totalcountchange: function() {
                    var tb = this.getDockedComponent('tbar'),
                        store = this.getStore(),
                        formatText = Ext.Object.getSize(this.filterHelper.getSetValues()) ? this.filteredUsersText: this.totalUsersText ,
                        cntText = Ext.String.format(formatText, store.getTotalCount());

                    tb.getComponent('countBtn').setText(cntText);
                },
                */
                scope: this
            }
        });
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
            width: 60,
            renderer: this.flagsRenderer
        }];
    },

    initMySelModel: function() {
        this.selModel = {
            selType: 'rowmodel',
            mode: 'MULTI'
        };
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
            this.tbarItems.push({
                itemId: 'deleteBtn',
                text: this.deleteUserText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                handler: this.deleteUser,
                scope: this,
                disabled: true
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
            rowdblclick: function(view, record){
                if (!Phlexible.User.isGranted('ROLE_USER_ADMIN_PATCH')) {
                    return;
                }
                var w = Ext.create('Phlexible.user.window.UserWindow', {
                    listeners: {
                        save: function(){
                            this.store.load();
                        },
                        scope: this
                    }
                });
                w.show(record);

            },
            scope: this
        });

    },

    addUser: function(){
        var record = Ext.create('Phlexible.user.model.User', {
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
                listeners: {
                    save: function() {
                        this.store.load();
                    },
                    scope: this
                }
            });

        win.show(record);
    },

    deleteUser: function() {
        var selectionModel = this.getSelectionModel(),
            records = selectionModel.getSelection(),
            ids = [],
            msg;

        if (records.length > 1) {
            msg = this.deleteUsersWarningText;
        } else {
            msg = Ext.String.format(this.deleteUserWarningText, records[0].get('username'));
        }

        for (var i=0; i<records.length; i++) {
            ids.push(records[i].get('id'));
        }

        if (!ids) {
            return;
        }

        Ext.MessageBox.confirm(this.deleteUserText, msg, function(btn) {
            if (btn !== 'yes') {
                return;
            }
            Ext.Ajax.request({
                url: Phlexible.Router.generate('phlexible_user_delete_user'),
                params: {
                    'ids[]': ids
                },
                success: function(response){
                    var data = Ext.decode(response.responseText);
                    if (data.success) {
                        this.getStore().load();
                    } else {
                        Phlexible.Notify.failure(data.message);
                    }
                },
                scope:this
            });
        }, this);
    },

    impersonateUser: function() {
        var selectionModel = this.getSelectionModel(),
            record = selectionModel.getSelection()[0];

        document.location.href = Phlexible.Router.generate('gui_index', {"_switch_user": record.get('username')});
    },

    flagsRenderer: function(v, md, r) {
        v = '';
        if (r.get('comment')) {
            v += Phlexible.Icon.inline('sticky-note-text', {'data-qtip': this.hasCommentsText});
        }
        if (r.get('disabled')) {
            v += Phlexible.Icon.inline('cross', {'data-qtip': this.isDisabledText});
        }
        if (r.get('expiresAt')) {
            v += Phlexible.Icon.inline('alarm-clock', {'data-qtip': this.hasExpireDateText});
        }
        return v;
    }
});
