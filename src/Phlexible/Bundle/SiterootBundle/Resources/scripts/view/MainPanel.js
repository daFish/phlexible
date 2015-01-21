Ext.define('Phlexible.siteroot.view.MainPanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.siteroot-main',

    title: '_MainPanel',
    iconCls: Phlexible.Icon.get('globe'),
    cls: 'p-siteroot-main-panel',
    border: false,
    layout: 'border',

    saveSiterootDataText: '_saveSiterootDataText',
    checkAccordionsForErrorsText: '_checkAccordionsForErrorsText',

    /**
     * Fires after the active Siteroot has been changed
     *
     * @event siterootChange
     * @param {Number} siterootId The ID of the selected ElementType.
     * @param {String} siterootTitle The Title of the selected ElementType.
     */

    /**
     *
     */
    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [{
            xtype: 'siteroot-list',
            region: 'west',
            itemId: 'list',
            width: 250,
            minWidth: 200,
            maxWidth: 350,
            split: false,
            padding: '5 0 5 5',
            listeners: {
                siterootChange: this.onSiterootChange,
                siterootDataChange: this.onSiterootDataChange,
                scope: this
            }
        }, {
            region: 'center',
            xtype: 'panel',
            itemId: 'accordion',
            title: 'no_siteroot_loaded',
            layout: 'accordion',
            disabled: true,
            padding: 5,
            tbar: [
                {
                    text: this.saveSiterootDataText,
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.SAVE),
                    handler: this.onSaveData,
                    scope: this
                }
            ],
            items: [
                {
                    xtype: 'siteroot-urls'
                },
                {
                    xtype: 'siteroot-titles'
                },
                {
                    xtype: 'siteroot-properties'
                },
                {
                    xtype: 'siteroot-specialtids'
                },
                {
                    xtype: 'siteroot-navigations'
                }
            ]
        }];
    },

    loadParams: function () {
    },

    /**
     * After the siteroot selection changes load the siteroot data.
     *
     * @param {Number} id
     * @param {String} title
     */
    onSiterootChange: function (id, title) {
        this.getComponent(0).enable();

        Ext.Ajax.request({
            url: Phlexible.Router.generate('siteroot_siteroot_load', {id: id}),
            success: function (response) {
                var data = Ext.decode(response.responseText);

                this.siterootId = id;
                this.siterootTitle = title;
                this.getComponent('accordion').setTitle(title);

                this.getComponent('accordion').items.each(function (panel) {
                    panel.loadData(id, title, data);
                });

                this.getComponent('accordion').enable();
            },
            scope: this
        })
    },

    /**
     * After the siteroot data changed.
     *  - new siteroot added
     *  - title of siteroot changed
     */
    onSiterootDataChange: function () {
        Phlexible.Frame.loadConfig();
        Phlexible.Frame.menu.load();
    },

    /**
     * If a complete siteroot should be saved (including all plugins).
     *
     * The data is collected and submitted in only one request to the server
     * all plugins must register themselfs at the PHP observer for handle the
     * submit process.
     */
    onSaveData: function () {
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
