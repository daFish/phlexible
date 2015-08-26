Ext.define('Phlexible.siteroot.view.List', {
    extend: 'Ext.grid.Panel',
    xtype: 'siteroot.list',

    iconCls: Phlexible.Icon.get('globe'),

    idText: '_idText',
    siterootsText: '_siterootsText',
    removeText: '_removeText',
    removeDescriptionText: '_removeDescriptionText',
    addSiterootText: '_addSiterootText',
    titleText: '_titleText',
    saveSiterootsText: '_saveSiterootsText',

    /**
     * Fires after the active Siteroot has been changed
     *
     * @event siterootChange
     * @param {Number} siteroot_id The ID of the selected siteroot.
     * @param {String} siteriit_title The Title of the selected siteroot.
     */

    /**
     * Fires after a siteroot is added or title has been changed
     *
     * @event siterootDataChange
     */

    /**
     *
     */
    initComponent: function () {
        this.initMyColumns();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyColumns: function() {
        this.columns = [
            {
                header: this.idText,
                hidden: true,
                dataIndex: 'id'
            }, {
                header: this.siterootsText,
                dataIndex: 'titles',
                flex: 1,
                sortable: true,
                renderer: function(v) {
                    return v.de;
                }
            }, {
                xtype: 'actioncolumn',
                width: 30,
                items: [{
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                    tooltip: this.removeText,
                    handler: function (grid, rowIndex, colIndex) {
                        var r = grid.getStore().getAt(rowIndex);

                        Ext.MessageBox.confirm(
                            this.removeText,
                            this.removeDescriptionText,
                            this.onDeleteSiteroot.createDelegate(this, [r], true)
                        );
                    },
                    scope: this
                }]
            }
        ];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'top',
            items: [{
                text: this.addSiterootText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                handler: this.onAddSiteroot,
                scope: this
            },'->',{
                text: this.saveSiterootsText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.SAVE),
                handler: this.onSaveSiteroots,
                scope: this
            }]
        }];
    },

    /**
     * If the siteroot store is loaded and no siteroot
     * is selected then select the first siteroot initially.
     *
     * @param {Object} store
     */
    onLoadStore: function (store) {
        var sm = this.getSelectionModel();

        if ((store.getCount() > 0)) {
            if (!this.selected) {
                sm.selectRange(0, 0);
            } else {
                var i = store.find('id', this.selected);
                this.selected = null;
                sm.select([i]);
            }
        }
    },

    /**
     * Action if site
     */
    onAddSiteroot: function () {
        Ext.MessageBox.prompt(this.addSiterootText, this.titleText, function (btn, text) {
            if (btn !== 'ok') {
                return;
            }

            Ext.Ajax.request({
                url: Phlexible.Router.generate('siteroot_create'),
                params: {
                    title: text
                },
                success: function (response) {
                    var data = Ext.decode(response.responseText);
                    if (data.success) {
                        this.fireEvent('siterootDataChange');
                    }
                    else {
                        Phlexible.Notify.failure(data.msg);
                    }
                },
                scope: this
            });
        }, this);
    },

    /**
     * Start deletion of record.
     *
     * @param {Object} btn
     * @param {String} text
     * @param {Object} r
     */
    onDeleteSiteroot: function (btn, text, x, r) {

        if (btn == 'yes') {
            Ext.Ajax.request({
                url: Phlexible.Router.generate('siteroot_delete'),
                params: {
                    id: r.id
                },
                success: function (response) {
                    var data = Ext.decode(response.responseText);
                    if (data.success) {
                        this.fireEvent('siterootDataChange');
                        Phlexible.Frame.menu.load();
                    }
                    else {
                        Ext.Msg.alert('Failure', data.msg);
                    }
                },
                scope: this
            });
        }
    },

    /**
     * If a complete siteroot should be saved (including all plugins).
     *
     * The data is collected and submitted in only one request to the server
     * all plugins must register themselfs at the PHP observer for handle the
     * submit process.
     */
    onSaveSiteroots: function () {
        this.fireEvent('save');
        return;

        var saveData = {}, valid = true;

        this.getComponent(1).items.each(function (panel) {
            if (typeof(panel.isValid) == 'function' && !panel.isValid()) {
                valid = false;
            }
            else if (typeof(panel.getSaveData) == 'function') {
                var data = panel.getSaveData();

                if (!data) {
                    return;
                }

                // merge data
                Ext.apply(saveData, data);
            }
        }, this);

        if (!valid) {
            Phlexible.Notify.failure(this.checkAccordionsForErrorsText);
            return;
        }

        // save data
        Ext.Ajax.request({
            method: 'POST',
            url: Phlexible.Router.generate('siteroot_save'),
            params: {
                id: this.siterootId,
                data: Ext.encode(saveData)
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);
                if (data.success) {
                    this.getSiterootGrid().selected = this.getSiterootGrid().getSelectionModel().getSelected().id;
                    this.getSiterootGrid().store.reload();

//                    this.onSiterootChange(this.siterootId, this.siterootTitle);
                }
                else {
                    Ext.Msg.alert('Failure', data.msg);
                }
            },
            scope: this
        });

    }

});