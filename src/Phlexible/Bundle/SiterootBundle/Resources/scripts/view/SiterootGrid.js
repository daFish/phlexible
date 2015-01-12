Ext.define('Phlexible.siteroots.SiterootGrid', {
    extend: 'Ext.grid.GridPanel',
    alias: 'widget.siteroots-list',

    title: Phlexible.siteroots.Strings.siteroot,
    strings: Phlexible.siteroots.Strings,

    /**
     * Fires after the active Siteroot has been changed
     *
     * @event siterootChange
     * @param {Number} siteroot_id The ID of the selected ElementType.
     * @param {String} siteriit_title The Title of the selected ElementType.
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
        this.store = new Ext.data.Store({
            model: 'Phlexible.siteroots.model.Siteroot',
            proxy: {
                type: 'ajax',
                url: Phlexible.Router.generate('siteroots_siteroot_list'),
                simpleSortMode: true,
                reader: {
                    type: 'json',
                    rootProperty: 'siteroots',
                    idProperty: 'id',
                    totalProperty: 'count'
                },
                extraParams: this.storeExtraParams
            },
            autoLoad: true,
            remoteSort: true,
            sorters: [{
                property: 'title',
                direction: 'ASC'
            }],
            listeners: {
                load: this.onLoadStore,
                scope: this
            }
        });

        this.columns = [
            {
                header: this.strings.id,
                hidden: true,
                dataIndex: 'id'
            },{
                header: this.strings.siteroots,
                dataIndex: 'title',
                flex: 1,
                sortable: true
            },{
                xtype: 'actioncolumn',
                width: 30,
                items: [{
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                    tooltip: this.strings.remove,
                    handler: function(grid, rowIndex, colIndex) {
                        var r = grid.getStore().getAt(rowIndex);

                        Ext.MessageBox.confirm(
                            this.strings.remove,
                            this.strings.sure,
                            this.onDeleteSiteroot.createDelegate(this, [r], true)
                        );
                    },
                    scope: this
                }]
            }
        ];

        this.tbar = [
            {
                text: this.strings.add_siteroot,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                handler: this.onAddSiteroot,
                scope: this
            }
        ];

        this.on({
            select: this.onSelectSiteroot,
            siterootDataChange: this.onSiterootDataChange,
            scope: this
        });

        this.callParent(arguments);
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
     * If the siteroot selection changes fire the siterootChange event.
     *
     * @param {Object} selModel
     * @param {Number} rowIndex
     * @param {Object} record
     */
    onSelectSiteroot: function (grid, record, rowIndex) {
        this.fireEvent('siterootChange', record.get('id'), record.get('title'));
    },

    /**
     * Action if site
     */
    onAddSiteroot: function () {
        Ext.MessageBox.prompt(this.strings.new_siteroot, this.strings.siteroot_title, function (btn, text) {
            if (btn !== 'ok') {
                return;
            }

            Ext.Ajax.request({
                url: Phlexible.Router.generate('siteroots_siteroot_create'),
                params: {
                    title: text
                },
                success: function (response) {
                    var data = Ext.decode(response.responseText);
                    if (data.success) {
                        this.fireEvent('siterootDataChange');
                    }
                    else {
                        Ext.Msg.alert(this.strings.failure, data.msg);
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
                url: Phlexible.Router.generate('siteroots_siteroot_delete'),
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
     * After the siteroot data changed.
     *  - new siteroot added
     *  - title of siteroot changed
     */
    onSiterootDataChange: function () {
        this.store.reload();
    }

});
