Ext.define('Phlexible.accesscontrol.RightsGrid', {
    extend: 'Ext.grid.GridPanel',
    alias: 'widget.accesscontrol-rights',

    title: '_RightsGrid',
    border: false,
    autoExpandColumn: 1,
    loadMask: true,
    stripeRows: true,
    emptyText: '_emptyText',

    objectType: null,
    languageEnabled: false,

    idText: '_idText',
    subjectText: '_subjectText',
    languageText: '_languageText',
    allText: '_allText',
    actionsText: '_actionsText',
    deleteText: '_deleteText',
    linkText: '_linkText',
    restoreText: '_restoreText',
    saveText: '_saveText',
    usersText: '_usersText',
    addText: '_addText',
    groupsText: '_groupsText',
    reloadText: '_reloadText',
    legendText: '_legendText',
    legendNotSetText: '_notSetText',
    legendSetHereText: '_setHereText',
    legendSetAboveText: '_setAboveText',
    legendStoppedAboveText: '_stoppedAboveText',
    legendStoppedHereText: '_stoppedHereText',
    legendStoppedBelowText: '_stoppedBelowText',

    getDefaultUrls: function () {
        return {
            users: Phlexible.Router.generate('accesscontrol_users'),
            groups: Phlexible.Router.generate('accesscontrol_groups'),
            permissions: Phlexible.Router.generate('accesscontrol_permissions'),
            save: Phlexible.Router.generate('accesscontrol_save')
        };
    },

    createIconCls: function(permission) {
        return 'p-accesscontrol-right-icon';
    },

    initComponent: function () {
        Ext.applyIf(this.urls, this.getDefaultUrls());

        if (!this.urls.users) {
            throw 'RightsGrid users URL config incomplete.';
        }

        if (!this.urls.groups) {
            throw 'RightsGrid groups URL config incomplete.';
        }

        if (!this.urls.identities) {
            throw 'RightsGrid identities URL config incomplete.';
        }

        if (!this.urls.permissions) {
            throw 'RightsGrid permissions URL config incomplete.';
        }

        if (!this.urls.add) {
            throw 'RightsGrid add URL config incomplete.';
        }

        if (!this.urls.save) {
            throw 'RightsGrid save URL config incomplete.';
        }

        if (!this.objectType) {
            throw 'RightsGrid objectType config incomplete.';
        }

        Ext.Ajax.request({
            url: this.urls.permissions,
            params: {
                objectType: this.objectType
            },
            success: function (response) {
                var fields = [
                    {
                        header: this.idText,
                        dataIndex: 'objectId',
                        width: 100,
                        hidden: true,
                        sortable: true
                    },
                    {
                        header: this.subjectText,
                        dataIndex: 'securityName',
                        width: 100,
                        sortable: true,
                        renderer: function (v, md, r) {
                            return Phlexible.inlineIcon('p-accesscontrol-' + r.data.type + '-icon') + ' ' + v;
                        }
                    }
                ];

                if (this.languageEnabled) {
                    var languageData = this.getLanguageData();

                    fields.push({
                        header: this.languageText,
                        dataIndex: 'language',
                        width: 100,
                        sortable: true,
                        renderer: function (v, md, r) {
                            var suffix = '';
                            if (r.data['new']) {
                                suffix += ' ' + Phlexible.inlineIcon('p-accesscontrol-new-icon');
                            }

                            if (!v || v == '_all_') {
                                return Phlexible.inlineIcon('p-accesscontrol-all-icon') + ' ' + this.allText + suffix;
                            }

                            Ext.each(Phlexible.Config.get('set.language.frontend'), function (item) {
                                if (item[0] === v) {
                                    v = Phlexible.inlineIcon(item[2]) + ' ' + item[1] + suffix;
                                    return false;
                                }
                            }, this);

                            return v;
                        }.createDelegate(this),
                        editor: new Ext.ux.IconCombo({
                            store: new Ext.data.SimpleStore({
                                fields: ['key', 'title', 'iconCls'],
                                data: languageData
                            }),
                            valueField: 'key',
                            displayField: 'title',
                            iconClsField: 'iconCls',
                            editable: false,
                            emptyText: '_select',
                            selectOnFocus: true,
                            mode: 'local',
                            typeAhead: true,
                            triggerAction: 'all',
                            listeners: {
                                beforeselect: function (combo, comboRecord, index) {
                                    var cancel = false;
                                    var subjectRecord = this.selModel.getSelected();
                                    this.store.each(function (record) {
                                        if (subjectRecord.id !== record.id &&
                                            subjectRecord.data.objectType == record.data.objectType &&
                                            subjectRecord.data.objectId == record.data.objectId) {
                                            if ((index < 1 && record.data.language != '_all_') ||
                                                (index >= 1 && (
                                                    record.data.language == '_all_' ||
                                                        record.data.language == comboRecord.data.key
                                                    )
                                                    )) {
                                                cancel = true;
                                                return false;
                                            }
                                        }
                                    });
                                    if (cancel) return false;
                                },
                                scope: this
                            }
                        })
                    });
                }

                var data = Ext.decode(response.responseText);
                var plugins = [];

                Ext.each(data.permissions, function(permission) {
                    var bit = permission.bit,
                        name = permission.name,
                        iconCls = this.createIconCls(permission);

                    fields.push({
                        header: Phlexible.inlineIcon(iconCls, {qtip: name}),
                        dataIndex: 'mask',
                        permission: permission,
                        width: 40,
                        renderer: function (v) {
                            if (bit & v) {
                                return Phlexible.inlineIcon('p-accesscontrol-checked-icon');
                            } else {
                                return Phlexible.inlineIcon('p-accesscontrol-unchecked-icon');
                            }

                            var s = '';
                            v[key]['status'] = parseInt(v[key]['status'], 10);
                            switch (v[key]['status']) {
                                case 0:
                                    s = Phlexible.inlineIcon('p-accesscontrol-stopped-icon');
                                    break;

                                case 1:
                                    s = Phlexible.inlineIcon('p-accesscontrol-single_right-icon');
                                    break;

                                case 2:
                                    s = Phlexible.inlineIcon('p-accesscontrol-checked-icon');
                                    break;

                                case 3:
                                    s = Phlexible.inlineIcon('p-accesscontrol-checked_inherit-icon');
                                    break;

                                case 4:
                                    s = Phlexible.inlineIcon('p-accesscontrol-unchecked_inherit-icon');
                                    break;

                                default:
                                    s = Phlexible.inlineIcon('p-accesscontrol-unchecked-icon');
                                    break;
                            }
                            return s;
                        }
                    });
                }, this);

                fields.push(this.actions);

                var cm = new Ext.grid.ColumnModel(fields);

                this.reconfigure(this.store, cm);
            },
            scope: this
        });

        /*
         this.expander = new Ext.grid.RowExpander({
         dataIndex: 'rights',
         tpl: new Ext.XTemplate(
         '{[for(var xyz in rights) {}]}',
         '<tpl for="rights">',
         '<p>x {#} {.}</p>',
         '{[Phlexible.console.log(values)]}',
         '</tpl>'
         )
         });
         */

        this.actions = new Ext.ux.grid.RowActions({
            header: this.actionsText,
            autoWidth: false,
            width: 70,
            actions: [
                {
                    //showIndex: 'setHere',
                    iconCls: 'p-accesscontrol-delete-icon',
                    tooltip: this.deleteText,
                    callback: this.deleteAction
                },
                {
                    //showIndex: 'inherited',
                    iconCls: 'p-accesscontrol-link-icon',
                    tooltip: this.linkText,
                    callback: this.linkAction
                },
                {
                    //showIndex: 'restore',
                    iconCls: 'p-accesscontrol-restore-icon',
                    tooltip: this.restoreText,
                    callback: this.restoreAction
                }
            ]
        });

        this.store = new Ext.data.JsonStore({
            url: this.urls.identities,
            root: 'identities',
            fields: Phlexible.accesscontrol.model.AccessControlEntry,
            baseParams: {
                objectType: this.objectType,
                objectId: null
            }
        });

        this.columns = [
            //this.expander,
            {
                header: this.idText,
                dataIndex: 'objectId',
                width: 30,
                hidden: true,
                sortable: true
            },
            {
                header: this.subjectText,
                dataIndex: 'securityName',
                sortable: true,
                renderer: function (v, md, r) {
                    return Phlexible.inlineIcon('p-accesscontrol-' + r.data.type + '-icon') + ' ' + v;
                }
            }/*,{
             header: this.languageText,
             dataIndex: 'language',
             width: 50,
             sortable: true,
             renderer: function(v) {
             return Phlexible.inlineIcon('p-gui-de-icon');
             }
             }*/
        ];

        this.selModel = new Ext.grid.RowSelectionModel({
            singleSelect: true
        });

        this.plugins = [
            //this.expander,
            this.actions
        ];

        this.tbar = [
            {
                text: this.saveText,
                iconCls: 'p-accesscontrol-save-icon',
                handler: this.onSave,
                scope: this
            },
            '-',
            {
                xtype: 'combo',
                listWidth: 300,
                store: new Ext.data.JsonStore({
                    url: this.urls.users,
                    root: 'data',
                    id: 'securityId',
                    totalProperty: 'total',
                    fields: Phlexible.accesscontrol.model.SecurityIdentity,
                    autoLoad: false
                }),
                pageSize: 20,
                displayField: 'securityName',
                valueField: 'securityId',
                emptyText: this.usersText,
                selectOnFocus: true,
                mode: 'remote',
                typeAhead: true,
                triggerAction: 'all'
            },
            {
                text: this.addText,
                iconCls: 'p-accesscontrol-add-icon',
                handler: function () {
                    var usersCombo = this.getTopToolbar().items.items[2];

                    this.onAdd(usersCombo);
                },
                scope: this
            },
            '-',
            {
                xtype: 'combo',
                listWidth: 300,
                store: new Ext.data.JsonStore({
                    url: this.urls.groups,
                    root: 'data',
                    id: 'securityId',
                    fields: Phlexible.accesscontrol.model.SecurityIdentity,
                    autoLoad: false
                }),
                displayField: 'securityName',
                valueField: 'securityId',
                emptyText: this.groupsText,
                selectOnFocus: true,
                mode: 'remote',
                typeAhead: true,
                triggerAction: 'all'
            },
            {
                text: this.addText,
                iconCls: 'p-accesscontrol-add-icon',
                handler: function () {
                    var groupsCombo = this.getTopToolbar().items.items[5];

                    this.onAdd(groupsCombo);
                },
                scope: this
            },
            '-',
            {
                text: this.reloadText,
                iconCls: 'p-accesscontrol-reload-icon',
                handler: function () {
                    this.store.reload();
                },
                scope: this
            }
        ];

        this.bbar = [
            {
                xtype: 'tbtext',
                text: this.legendText
            },
            '-',
            {
                xtype: 'tbtext',
                text: Phlexible.inlineIcon('p-accesscontrol-unchecked-icon') + ' ' + this.legendNotSetText
            },
            ' ',
            {
                xtype: 'tbtext',
                text: Phlexible.inlineIcon('p-accesscontrol-checked-icon') + ' ' + this.legendSetHereText
            },
            ' ',
            {
                xtype: 'tbtext',
                text: Phlexible.inlineIcon('p-accesscontrol-checked_inherit-icon') + ' ' + this.legendSetAboveText
            },
            ' ',
            {
                xtype: 'tbtext',
                text: Phlexible.inlineIcon('p-accesscontrol-unchecked_inherit-icon') + ' ' + this.legendStoppedAboveText
            },
            ' ',
            {
                xtype: 'tbtext',
                text: Phlexible.inlineIcon('p-accesscontrol-stopped-icon') + ' ' + this.legendStoppedHereText
            },
            ' ',
            {
                xtype: 'tbtext',
                text: Phlexible.inlineIcon('p-accesscontrol-single_right-icon') + ' ' + this.legendStoppedBelowText
            }
        ];

        this.on({
            beforeedit: function (e) {
                if (!e.record.data['new']) {
                    return false;
                }
            },
            cellclick: function (grid, rowIndex, colIndex) {
                if (colIndex < (this.languageEnabled ? 3 : 2) || colIndex == grid.getColumnModel().getColumnCount() - 1) return;

                var record = grid.getStore().getAt(rowIndex);
                var cm = grid.getColumnModel();
                var col = cm.getColumnById(cm.getColumnId(colIndex));
                var permission = col.permission;
                var mask = record.data.mask;

                if (mask & permission.bit) {
                    mask = mask ^ permission.bit;
                } else {
                    mask = mask | permission.bit;
                }

                record.set('mask', mask);
                return;

                var original = record.data.original;
                var above = record.data.above;
                var status = parseInt(rights[right]['status'], 10);
                var originalStatus = parseInt(original[right]['status'], 10);
                var aboveStatus = parseInt(above[right]['status'], 10);

                //Phlexible.console.log('right: ' + right);
                //Phlexible.console.log('status: ' + status);
                //Phlexible.console.log('originalStatus: ' + originalStatus);
                //Phlexible.console.log('aboveStatus: ' + aboveStatus);

                switch (status) {
                    case 2:
                    case 3:
                        status = 1;
                        break;

                    case 1:
                        if (aboveStatus === 2 || aboveStatus === 3) {
                            status = 0;
                        }
                        else if (aboveStatus === 4) {
                            status = 4;
                        }
                        else if (originalStatus === 4) {
                            status = 4;
                        }
                        else {
                            status = -1;
                        }
                        break;

                    default:
                        if (aboveStatus === 2 || aboveStatus === 3) {
                            status = aboveStatus;
                        }
                        else if (originalStatus === 2 || originalStatus === 3) {
                            status = originalStatus;
                        }
                        else {
                            status = 2;
                        }
                        break;
                }

                rights[right]['status'] = status;

                record.beginEdit();
                //r.set('rights', rights);
                var dummy = record.data.label;
                record.set('label', 'xxx');
                record.set('label', dummy);
                record.set('restore', 1);
                record.set('inherited', !record.get('setHere'));
                record.endEdit();
            },
            scope: this
        });

        Phlexible.accesscontrol.RightsGrid.superclass.initComponent.call(this);
    },

    doLoad: function (objectType, objectId) {
        if (this.objectType !== objectType || this.objectId !== objectId) {
            this.objectType = objectType;
            this.objectId = objectId;

            this.store.removeAll();

            //this.selModel.clearSelections();

            this.store.baseParams = {
                objectType: this.objectType,
                objectId: objectId
            };
            this.store.load();
        }

        this.enable();
    },

    getLanguageData: function () {
        return [];
    },

    deleteAction: function (grid, record, action, row, col) {
        grid.store.remove(record);
    },

    linkAction: function (grid, record, action, row, col) {
        var empty = true;
        for (var i in record.data.above) {
            if (record.data.above[i].status != -1) {
                empty = false;
                break;
            }
        }

        if (empty) {
            grid.store.remove(record);
        }
        else {
            var above = record.get('above');
            var rights = record.get('rights');

            rights = Ext.clone(above);

            record.beginEdit();
            record.set('rights', false);
            record.set('rights', rights);
            record.set('restore', 1);
            record.set('inherited', 0);
            var dummy = record.data.label;
            record.set('label', 'xxx');
            record.set('label', dummy);
            record.endEdit();
            //record.commit();
        }

        /*
         grid.deletedSubjects.push({
         content_type: grid.content_type,
         content_id: grid.content_id,
         object_type: record.data.object_type,
         object_id: record.data.object_id,
         language: record.data.language
         });
         */
    },

    restoreAction: function (grid, record, action, row, col) {
        var original = record.get('original');
        var rights = record.get('rights');

        rights = Ext.clone(original);

        record.beginEdit();
        record.set('rights', false);
        record.set('rights', rights);
        record.set('restore', 0);
        record.set('inherited', !record.get('setHere'));
        var dummy = record.data.label;
        record.set('label', 'xxx');
        record.set('label', dummy);
        record.endEdit();
        record.commit();
    },

    onAdd: function (combo) {
        var securityIdentity = combo.getStore().getById(combo.getValue()),
            entry = new Phlexible.accesscontrol.model.AccessControlEntry({
                id: null,
                objectType: this.objectType,
                objectId: this.objectId,
                securityType: securityIdentity.get('securityType'),
                securityId: securityIdentity.get('securityId'),
                securityName: securityIdentity.get('securityName'),
                mask: 0,
                stopMask: 0,
                noInheritMask: 0
            });

        this.store.insert(0, entry);
        return;

        var key = combo.getValue();
        var r = combo.getStore().getById(key);
        combo.clearValue();
        if (!r) {
            return;
        }
        var language = '_all_';

        Ext.Ajax.request({
            url: this.urls.add,
            params: {
                objectType: this.objectType,
                objectId: this.objectId,
                securityType: r.data.securityType,
                securityId: r.data.securityId
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                var newRecord = new Ext.data.Record({
                    type: data.type,
                    objectType: data.objectType,
                    objectId: data.objectId,
                    label: data.label,
                    rights: data.rights,
                    original: data.original,
                    above: data.above,
                    language: language,
                    inherited: 0,
                    setHere: 1,
                    restore: 0,
                    'new': 1
                });

                this.store.insert(0, newRecord);
            },
            scope: this
        });
    },

    onSave: function () {
        var identities = [];

        this.store.each(function(r) {
            identities.push(r.data);
        });

        Ext.Ajax.request({
            url: this.urls.save,
            params: {
                objectType: this.objectType,
                objectId: this.objectId,
                identities: Ext.encode(identities)
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    Phlexible.success(data.msg);

                    this.store.reload();

                    this.store.commitChanges();
                }
                else {
                    Ext.MessageBox.alert('Warning', data.msg);
                }
            },
            scope: this
        });
    }
});
