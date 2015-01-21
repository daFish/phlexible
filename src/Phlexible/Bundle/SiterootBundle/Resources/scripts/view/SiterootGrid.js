Ext.define('Phlexible.siteroot.view.SiterootGrid', {
    extend: 'Ext.grid.GridPanel',
    alias: 'widget.siteroot-list',

    title: '_SiterootGrid',

    idText: '_idText',
    siterootsText: '_siterootsText',
    removeText: '_removeText',
    removeDescriptionText: '_removeDescriptionText',
    addSiterootText: '_addSiterootText',
    titleText: '_titleText',

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
        this.initMyStore();
        this.initMyColumns();
        this.initMyDockedItems();
        this.initMyListeners();

        this.callParent(arguments);
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.siteroot.model.Siteroot',
            proxy: {
                type: 'ajax',
                url: Phlexible.Router.generate('siteroot_list'),
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
    },

    initMyColumns: function() {
        this.columns = [
            {
                header: this.idText,
                hidden: true,
                dataIndex: 'id'
            }, {
                header: this.siterootsText,
                dataIndex: 'title',
                flex: 1,
                sortable: true
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
            }]
        }]
    },

    initMyListeners: function() {
        this.on({
            select: this.onSelectSiteroot,
            siterootDataChange: this.onSiterootDataChange,
            scope: this
        });
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
     * After the siteroot data changed.
     *  - new siteroot added
     *  - title of siteroot changed
     */
    onSiterootDataChange: function () {
        this.store.reload();
    }

});
