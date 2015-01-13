/**
 * Dashboard
 */
Ext.define('Phlexible.dashboard.view.Dashboard', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.dashboard-dashboard',

    title: '_dashboard',
    cls: 'p-dashboard-main-panel',
    header: false,
    border: false,
    layout: 'fit',

    cols: 3,

    noTitleText: '_no_title',
    noDescriptionText: '_no_description',

    initComponent: function() {
        if (Phlexible.App.getConfig().has('dashboard.columns')) {
            this.cols = Phlexible.App.getConfig().get('dashboard.columns');
        }

        this.iconCls = Phlexible.Icon.get('dashboard');

        this.initMyItems();
        this.initMyTasks();

        this.callParent(arguments);
    },

    initMyTasks: function() {
        this.saveTask = new Ext.util.DelayedTask(this.doSave, this);
    },

    initMyItems: function() {
        this.items = [{
            xtype: 'dashboard-portalpanel',
            region: 'center',
            itemId: 'portalPanel',
            border: false,
            cols: this.cols,

            listeners: {
                portletAdd: this.save,
                portletClose: function(panel, item) {
                    var store = Ext.data.StoreManager.lookup('dashboard-available'),
                        record = store.getById(item.id);
                    record.set('hidden', false);
                    store.sort('title', 'ASC');

                    this.save();
                },
                portletCollapse: this.save,
                portletExpand: this.save,
                drop: this.save,
                scope: this
            }
        }];
    },

    onRender: function() {
        this.callParent(arguments);

        Ext.Ajax.request({
            url: Phlexible.Router.generate('dashboard_portlets'),
            success: this.onLoadSuccess,
            scope: this
        });
    },

    onLoadSuccess: function(response) {
        var data = Ext.decode(response.responseText),
            cols = this.getComponent('portalPanel').cols,
            i, row, r, id;

        // always add welcome infobar
        this.addDocked({
            xtype: 'dashboard-infobar-welcome',
            dock: 'top',
            data: {},
            listeners: {
                addPortlet: function() {
                    Ext.create('Phlexible.dashboard.view.ListWindow', {
                        listeners: {
                            portletOpen: function(item){
                                this.getComponent('portalPanel').addPortlet(item);
                            },
                            scope: this
                        }
                    }).show();
                },
                editColumns: function() {
                    Ext.create('Phlexible.dashboard.view.ColumnsWindow').show();
                },
                scope: this
            }
        });

        if (data.headerBar) {
            for (i = 0; i < data.headerBar.length; i++) {
                row = data.headerBar[i];

                this.addDocked({
                    xtype: row.xtype,
                    type: row.type,
                    data: row.data,
                    dock: 'top'
                });
            }
        }

        if (data.footerBar) {
            for (i = 0; i < data.footerBar.length; i++) {
                row = data.footerBar[i];

                this.addDocked({
                    xtype: row.xtype,
                    type: row.type,
                    data: row.data,
                    dock: 'bottom',
                    padding: 5
                });
            }
        }

        this.configure(cols, data.portlets);
    },

    configure: function(cols, portlets) {
        var matrix = [],
            id, col, pos, mode, cls, record;

        for (i = 0; i < cols; i++) {
            matrix.push(new Ext.util.MixedCollection());
        }

        for(i = 0; i < portlets.length; i++) {
            row = portlets[i];
            id = row.id;

            cls = Ext.ClassManager.getByAlias('widget.' + row.xtype);
            if (!cls) {
                Phlexible.console.warn('widget.' + row.xtype + ' not found.');
                continue;
            }
            row.title = cls.prototype.title || this.noTitleText;
            row.description = cls.prototype.description || this.noDescriptionText;
            row.hidden = false;
            col = false;
            pos = false;
            mode = 'closed';

            if (Phlexible.App.getConfig().get('dashboard.portlets')[id]) {
                row.hidden = true;
                col  = parseInt(Phlexible.App.getConfig().get('dashboard.portlets')[id].col, 10);
                pos  = parseInt(Phlexible.App.getConfig().get('dashboard.portlets')[id].pos, 10);
                mode = Phlexible.App.getConfig().get('dashboard.portlets')[id].mode || 'opened';
            }

            record = Ext.create('Phlexible.dashboard.model.Portlet', row);
            Ext.data.StoreManager.lookup('dashboard-available').add(record);

            row.col = col;
            row.pos = pos;
            row.mode = mode;

            if (row.col !== false && row.pos !== false && row.mode !== 'closed') {
                if (row.col <= (cols - 1)) {
                    matrix[row.col].insert(row.pos, row);
                } else {
                    matrix[0].insert(row.pos, row);
                }
            }
        }

        for(i = 0; i< cols; i++) {
            matrix[i].each(function(item) {
                this.getComponent('portalPanel').addPortlet(item, true);
            }, this);
        }
    },

    reconfigure: function() {

    },

    save: function() {
        this.saveTask.cancel();
        this.saveTask.delay(2000);
    },

    doSave: function() {
        this.saveTask.cancel();

        var data = this.getComponent('portalPanel').getSaveData();

        Ext.Ajax.request({
            url: Phlexible.Router.generate('dashboard_portlets_save'),
            params: {
                portlets: Ext.encode(data)
            },
            success: function(response) {
                var data = Ext.decode(response.responseText);
                if (!data.success) {
                    Phlexible.Notify.failure(data.msg);
                }
            }
        });
    }
});
