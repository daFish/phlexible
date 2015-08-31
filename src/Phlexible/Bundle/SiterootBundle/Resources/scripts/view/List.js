Ext.define('Phlexible.site.view.List', {
    extend: 'Ext.grid.Panel',
    xtype: 'site.list',

    iconCls: Phlexible.Icon.get('globe'),
    emptyText: '_emptyText',

    idText: '_idText',
    sitesText: '_sitesText',
    removeText: '_removeText',
    removeDescriptionText: '_removeDescriptionText',
    addSiteText: '_addSiteText',
    titleText: '_titleText',
    saveSitesText: '_saveSitesText',

    /**
     * Fires after the active Site has been changed
     *
     * @event siteChange
     * @param {Number} site_id The ID of the selected site.
     * @param {String} siter_title The Title of the selected site.
     */

    /**
     * Fires after a site is added or title has been changed
     *
     * @event siteDataChange
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
                header: this.sitesText,
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
                            this.onDeleteSite.createDelegate(this, [r], true)
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
                text: this.addSiteText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                handler: this.onAddSite,
                scope: this
            },'->',{
                text: this.saveSitesText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.SAVE),
                handler: this.onSaveSites,
                scope: this
            }]
        }];
    },

    /**
     * If the site store is loaded and no site
     * is selected then select the first site initially.
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
    onAddSite: function () {
        Ext.MessageBox.prompt(this.addSiteText, this.titleText, function (btn, text) {
            if (btn !== 'ok') {
                return;
            }

            var site = Ext.create('Phlexible.site.model.Site', {
                hostname: text,
                titles: {
                    de: text,
                    en: text
                },
                createdAt: new Date(),
                createdBy: Phlexible.User.getUsername(),
                modifiedAt: new Date(),
                modifiedBy: Phlexible.User.getUsername()
            });

            this.store.add(site);
        }, this);
    },

    /**
     * Start deletion of record.
     *
     * @param {Object} btn
     * @param {String} text
     * @param {Object} r
     */
    onDeleteSite: function (btn, text, x, r) {

        if (btn == 'yes') {
            Ext.Ajax.request({
                url: Phlexible.Router.generate('site_delete'),
                params: {
                    id: r.id
                },
                success: function (response) {
                    var data = Ext.decode(response.responseText);
                    if (data.success) {
                        this.fireEvent('siteDataChange');
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
     * If a complete site should be saved (including all plugins).
     *
     * The data is collected and submitted in only one request to the server
     * all plugins must register themselfs at the PHP observer for handle the
     * submit process.
     */
    onSaveSites: function () {
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
            url: Phlexible.Router.generate('site_save'),
            params: {
                id: this.siteId,
                data: Ext.encode(saveData)
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);
                if (data.success) {
                    this.getSiteGrid().selected = this.getSiteGrid().getSelectionModel().getSelected().id;
                    this.getSiteGrid().store.reload();

//                    this.onSiteChange(this.siteId, this.siteTitle);
                }
                else {
                    Ext.Msg.alert('Failure', data.msg);
                }
            },
            scope: this
        });

    }

});
