/**
 * Dashboard
 */
Ext.define('Phlexible.dashboard.view.DashboardController', {
    extend: 'Ext.app.ViewController',

    requires: [
        'Phlexible.dashboard.infobar.Welcome',
        'Phlexible.dashboard.window.ListWindow',
        'Phlexible.dashboard.window.ColumnsWindow',
        'Phlexible.dashboard.store.Portlets',
        'Phlexible.dashboard.model.Portlet',
        'Ext.DomHelper'
    ],

    alias: 'controller.dashboard.dashboard',

    cols: 3,
    store: Ext.create('Phlexible.dashboard.store.Portlets'),

    noTitleText: '_noTitleText',
    noDescriptionText: '_noDescriptionText',

    init: function() {
        Phlexible.Logger.debug('DashboardController.init()');

        /*
        Phlexible.App.on('initPoller', function(poller) {
            poller.on('message', this.processMessage, this);
        });
        */

        if (Phlexible.Config.has('dashboard.columns')) {
            this.cols = Phlexible.Config.get('dashboard.columns');
        }

        for (var i = 0; i < this.cols; i += 1) {
            this.getView().add({
                itemId: 'col' + i,
                col: i,
                columnWidth: 1 / this.cols,
                padding: 10
            });
        }

        this.initMyTasks();

        Ext.Ajax.request({
            url: Phlexible.Router.generate('dashboard_portlets'),
            success: this.onLoadSuccess,
            scope: this
        });

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [];

        for (var i = 0; i < this.cols; i += 1) {
            this.items.push({
                itemId: 'col' + i,
                col: i,
                flex: 1,
                padding: 10
            });
        }
    },

    initMyListeners: function() {
        this.on({
            render: function() {
                Ext.DomHelper.append(this.el, {
                    tag: 'img',
                    src: '/bundles/phlexiblegui/images/watermark.gif',
                    cls: 'p-dashboard-watermark'
                });

                if (Phlexible.App.isGranted('ROLE_ADMIN')) {
                    Ext.DomHelper.append(this.el, {
                        tag: 'div',
                        cls: 'p-dashboard-info'
                    }, true).load({
                        url: Phlexible.Router.generate('dashboard_info', {extjs: Ext.versions.extjs.version})
                    });
                }
            },
            scope: this
        });
    },

    updatePanels: function() {
        for(var i=0; i<this.store.getCount(); i++){
            var r = this.store.getAt(i);
            this.addRecordPanel(r);
        }

        this.doLayout();
    },

    getColumn: function(pos) {
        return this.getView().items.get(pos);
    },

    getBestColumn: function() {
        var max = 999,
            bestColumn;

        this.getView().items.each(function(column) {
            if (column.xtype !== 'dashboard-column') {
                return;
            }
            if (!column.items.getCount()) {
                bestColumn = column;
                return false;
            }
            var cnt = column.getSize().height;
            if (cnt < max) {
                max = cnt;
                bestColumn = column;
            }
        });

        return bestColumn;
    },

    addPortlet: function(item, skipEvent) {
        var col;
        if (item.col !== false && item.col < this.cols) {
            col = this.getColumn(item.col);
        } else {
            col = this.getBestColumn();
        }

        if (item.xtype) {
            item.col = col.col;
            item.pos =  col.items.length + 1;

            var tools = [];
            var plugins = [];
            if (item.configuration && item.configuration.length) {
                tools.push({
                    id: 'gear',
                    portletId: item.id,
                    portletConfig: item.configuration,
                    handler: function(event, toolEl){
                        var w = Ext.xcreate('Phlexible.dashboard.ConfigWindow', {
                            portletId: toolEl.portletId,
                            portletConfig: toolEl.portletConfig
                        });
                        w.show();
                    },
                    scope: this
                });
            }

            var o = {
                xtype: item.xtype,
                id: item.id,
                item: item,
                collapsed: item.mode == 'collapsed',
                tools: tools,
                plugins: plugins,
                listeners: {
                    close: function(panel) {
                        panel.ownerCt.remove(panel, true);
                        panel.destroy();
                        this.fireEvent('portletClose', panel, panel.item);
                    },
                    collapse: function(panel){
                        panel.item.mode = 'collapsed';

                        this.fireEvent('portletCollapse', panel, panel.item);
                    },
                    expand: function(panel){
                        panel.item.mode = 'expanded';

                        this.fireEvent('portletExpand', panel, panel.item);
                    },
                    scope: this
                }
            };
            var panel = col.add(o);

            if (!skipEvent) {
                this.fireEvent('portletAdd', item, item.record);
            }
        }
    },

    getSaveData: function() {
        var data = {}, x = 0, y = 0;

        this.items.each(function(col) {
            col.items.each(function(item) {
                data[item.id] = {
                    id: item.id,
                    mode: item.item.mode,
                    col: x,
                    pos: y
                };
                y++;
            }, this);
            x++;
            y = 0;
        }, this);

        return data;
    },

    processMessage: function(event){
        if(typeof event == "object" && event.type == "dashboard") {
            var data = event.data;

            var r;
            for(var id in data) {
                var panel = this.panels[id];

                if(!panel || !panel.updateData) {
                    continue;
                }

                panel.updateData(data[id]);
            }
        }
    },

    initMyTasks: function() {
        this.saveTask = new Ext.util.DelayedTask(this.doSave, this);
    },

    onLoadSuccess: function(response) {
        var data = Ext.decode(response.responseText),
            cols = this.cols,
            i, row, r, id;

        // always add welcome infobar
        this.getView().addDocked({
            xtype: 'dashboard-infobar-welcome',
            dock: 'top',
            data: {},
            listeners: {
                addPortlet: function() {
                    Ext.create('Phlexible.dashboard.window.ListWindow', {
                        listeners: {
                            portletOpen: function(item){
                                this.addPortlet(item);
                            },
                            scope: this
                        }
                    }).show();
                },
                editColumns: function() {
                    Ext.create('Phlexible.dashboard.window.ColumnsWindow').show();
                },
                scope: this
            }
        });

        if (data.headerBar) {
            for (i = 0; i < data.headerBar.length; i++) {
                row = data.headerBar[i];

                this.getView().addDocked({
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

                this.getView().addDocked({
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
            row.imageUrl = cls.prototype.imageUrl || '/bundles/phlexibledashboard/images/portlet-plain.png';
            row.hidden = false;
            col = false;
            pos = false;
            mode = 'closed';

            if (Phlexible.Config.get('dashboard.portlets')[id]) {
                row.hidden = true;
                col  = parseInt(Phlexible.Config.get('dashboard.portlets')[id].col, 10);
                pos  = parseInt(Phlexible.Config.get('dashboard.portlets')[id].pos, 10);
                mode = Phlexible.Config.get('dashboard.portlets')[id].mode || 'opened';
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
                this.addPortlet(item, true);
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

        var data = this.getSaveData();

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
