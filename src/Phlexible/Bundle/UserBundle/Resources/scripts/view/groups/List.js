/**
 * Groups main view
 */
Ext.define('Phlexible.user.view.groups.List', {
    extend: 'Ext.grid.Panel',
    xtype: 'user.groups.list',

    cls: 'p-group-list',
    padding: 5,
    selModel: {
        selType: 'rowmodel',
        mode: 'MULTI'
    },

    idText: '_gid',
    groupsText: '_groups',
    groupText: '_group',
    membersText: '_members',
    commentText: '_comment',
    addGroupText: '_add_group',
    deleteGroupText: '_delete_group',
    saveText: '_save',
    resetText: '_reset',

    initComponent: function() {
        this.initMyColumns();
        this.initMyPlugins();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyColumns: function() {
        this.columns = [{
            header: this.idText,
            dataIndex: 'id',
            hidden: true,
            width: 250
        }, {
            header: this.groupText,
            dataIndex: 'name',
            flex: 1,
            field: 'textfield',
            //sortable: true,
            width: 300
        }, {
            header: this.membersText,
            dataIndex: 'memberCnt',
            //sortable: true,
            width: 100
        }, {
            header: this.commentText,
            dataIndex: 'comment',
            //sortable: true,
            width: 150
        }, {
            xtype: 'actioncolumn',
            width: 30,
            items: [{
                iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                tooltip: this.deleteText,
                handler: function (grid, rowIndex, colIndex, item, e, group) {
                    if (group) {
                        this.store.remove(group);
                    }
                },
                scope: this
            }]
        }];
    },

    initMyPlugins: function() {
        this.plugins = [{
            ptype: 'rowexpander',
            rowBodyTpl: [
                '<div></div>'
                /*'<div style="padding: 10px;">',
                 '<div style="font-weight: bold; padding-bottom: 10px;">' + MWF.strings.Users.members + ':</div>',
                 '<div>',
                 '<ul style="list-style-type: disc; padding-left: 25px;">',
                 '<tpl for="members">',
                 '<li>{.}</li>',
                 '</tpl>',
                 '</ul>',
                 '</div>',
                 '</div>'*/
            ]
        },
            Ext.create('Ext.grid.plugin.RowEditing', {
                clicksToEdit: 1
            })
        ];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'top',
            items: [{
                text: this.addGroupText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                handler: function() {
                    var group = Ext.create('Phlexible.user.model.Group', {
                        name: 'New Group',
                        members: 0,
                        createdAt: new Date(),
                        createdBy: Phlexible.User.getUsername(),
                        modifiedAt: new Date(),
                        modifiedBy: Phlexible.User.getUsername()
                    });
                    this.store.add(group);
                },
                scope: this
            },'-',{
                text: this.saveText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.SAVE),
                // disabled: true,
                handler: function() {
                    this.store.sync();
                },
                scope: this
            },{
                text: this.resetText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.RESET),
                // disabled: true,
                handler: function() {
                    this.store.rejectChanges();
                },
                scope: this
            }]
        }];
    }
});
