/**
 * Groups main view
 */
Ext.define('Phlexible.user.view.groups.Main', {
    extend: 'Ext.Panel',
    requires: [
        'Phlexible.user.model.Group'
    ],
    xtype: 'user.groups.main',

    iconCls: Phlexible.Icon.get('users'),
    cls: 'p-group-main',
    layout: 'fit',
    border: false,

    gidText: '_gid',
    groupsText: '_groups',
    groupText: '_group',
    membersText: '_members',
    commentText: '_comment',
    addGroupText: '_add_group',
    deleteGroupText: '_delete_group',
    saveText: '_save',
    resetText: '_reset',

    initComponent: function() {
        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [{
            xtype: 'grid',
//            title: this.groupsText,
//            viewConfig: {
//                forceFit: true
//            },
            selModel: {
                selType: 'rowmodel',
                mode: 'MULTI'
            },
            /*
             sm: new Ext.grid.RowSelectionModel({
             singleSelect: true,
             listeners: {
             selectionchange: {
             fn: function(sm) {
             var r = sm.getSelected();

             if(r && !r.get('readonly')) {
             this.getTopToolbar().items.items[1].enable();
             } else {
             this.getTopToolbar().items.items[1].disable();
             }
             },
             scope: this
             }
             }
             }),
             */
            store: Ext.create('Ext.data.Store', {
                model: 'Phlexible.user.model.Group',
                proxy: {
                    type: 'ajax',
                    url: Phlexible.Router.generate('phlexible_user_get_groups'),
                    reader: {
                        type: 'json',
                        rootProperty: 'groups'
                    }
                },
                autoLoad: true
            }),
            columns: [{
                header: this.gidText,
                dataIndex: 'gid',
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
            }],
            plugins: [{
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
            ],
            listeners: {
                beforeedit: function (e) {
                    if (e.record.data.readonly) {
                        return false;
                    }
                },
//                rowdblclick:  function(grid, rowIndex) {
//                    var r = grid.store.getAt(rowIndex);
//                    this.getComponent(1).store.baseParams = {
//                        role: r.get('id')
//                    };
//                    this.getComponent(1).store.reload();
//                },
                scope: this
            }
        }];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'top',
            items: [{
                text: this.addGroupText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                handler: function() {
                    var r = Ext.create('Phlexible.user.model.Group', {
                        gid: new Ext.ux.GUID().toString(),
                        name: 'New Group',
                        readonly: false,
                        members: 0
                    });
                    this.getComponent(0).store.add(r);
                },
                scope: this
            },{
                text: this.deleteGroupText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                disabled: true,
                handler: function() {
                    var r = this.getComponent(0).getSelectionModel().getSelected();

                    if (r) {
                        this.getComponent(0).store.remove(r);
                    }
                },
                scope: this
            },'-',{
                text: this.saveText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.SAVE),
                // disabled: true,
                handler: function() {
                    var range = this.getComponent(0).store.getRange();

                    var data = [];
                    for(var i=0; i<range.length; i++) {
                        data.push({
                            gid: range[i].data.gid,
                            name: range[i].data.name
                        });
                    }

                    Ext.Ajax.request({
                        url: Phlexible.Router.generate('phlexible_user_post_groups'),
                        params: {
                            data: Ext.encode(data)
                        },
                        success: function(response) {
                            var data = Ext.decode(response.responseText);

                            if(data.success) {
                                this.getComponent(0).store.reload();
                            } else {
                                Phlexible.Notify.failure(data.msg);
                            }

                        },
                        scope: this
                    });
                },
                scope: this
            },{
                text: this.resetText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.RESET),
                // disabled: true,
                handler: function() {
                    this.getComponent(0).store.reload();
                },
                scope: this
            }]
        }];
    }
});
