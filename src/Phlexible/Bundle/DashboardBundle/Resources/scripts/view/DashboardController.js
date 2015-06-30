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

        Phlexible.dashboard.Portlets = Ext.create('Phlexible.dashboard.store.Portlets');
        Phlexible.PluginManager.each('portlet', function(item) {
            Phlexible.dashboard.Portlets.add(Ext.create('Phlexible.dashboard.model.Portlet', item));
        });

        Phlexible.App.getPoller().on('message', this.processMessage, this);

        this.cols = Phlexible.Config.get('dashboard.defaults.columns');
        if (Phlexible.User.getProperty('dashboard.columns')) {
            this.cols = Phlexible.User.getProperty('dashboard.columns');
        }
        this.activePortlets = Phlexible.Config.get('dashboard.defaults.portlets');
        if (Phlexible.User.getProperty('dashboard.portlets')) {
            this.activePortlets = Phlexible.User.getProperty('dashboard.portlets');
        }

        this.configure(this.cols, this.activePortlets);

        this.initMyDockedItems();
        this.initMyTasks();

        /*
        Ext.Ajax.request({
            url: Phlexible.Router.generate('dashboard_portlets'),
            success: this.onLoadSuccess,
            scope: this
        });
        */

        this.callParent(arguments);
    },

    initMyDockedItems: function() {
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

    onAddPortlet: function() {
        Ext.create('Phlexible.dashboard.window.ListWindow', {
            listeners: {
                portletOpen: function(item) {
                    this.addPortlet(item);
                },
                scope: this
            }
        }).show();
    },
    onEditColumns: function() {
        Ext.create('Phlexible.dashboard.window.ColumnsWindow', {
            listeners: {
                columnsChange: function(columns) {
                    this.reconfigure(columns, this.activePortlets);
                },
                scope: this
            }
        }).show();
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

            var config = {
                xtype: item.xtype,
                itemId: item.id,
                reference: item.id,
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
            var panel = col.add(config);

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
        if (Ext.isObject(event) && event.type == "dashboard") {
            Ext.Object.each(event.data, function(id, data) {
                    var panel = this.lookupReference(id);

                if (!panel || !panel.updateData) {
                    return;
                }

                panel.updateData(data);
            }, this);
        }
    },

    initMyTasks: function() {
        this.saveTask = new Ext.util.DelayedTask(this.doSave, this);
    },

    configure: function(cols, activePortlets) {
        var matrix = [],
            id, col, portletConfig, pos, mode, cls, portlet, i;

        for (i = 0; i < cols; i += 1) {
            this.getView().add({
                itemId: 'col' + i,
                col: i,
                columnWidth: 1 / this.cols,
                padding: 10
            });
            matrix.push(new Ext.util.MixedCollection());
        }

        Ext.Object.each(activePortlets, function(portletId, activePortlet) {
            var portlet = Phlexible.dashboard.Portlets.findRecord('id', portletId),
                portletConfig;

            if (!portlet) {
                Phlexible.Logger.warn('Portlet ' + portletId + ' not found.');
                return;
            }

            portlet.set('hidden', true);

            portletConfig = Ext.clone(portlet.data);
            portletConfig.col = parseInt(activePortlet.col, 10);
            portletConfig.pos = parseInt(activePortlet.pos, 10);
            portletConfig.mode = activePortlet.mode || 'opened';

            if (portletConfig.col !== false && portletConfig.pos !== false && portletConfig.mode !== 'closed') {
                if (portletConfig.col <= (cols - 1)) {
                    matrix[portletConfig.col].insert(portletConfig.pos, portletConfig);
                } else {
                    matrix[0].insert(portletConfig.pos, portletConfig);
                }
            }
        });

        /*
        for (i = 0; i < portlets.length; i++) {
            portletConfig = portlets[i];
            id = portletConfig.id;

            cls = Ext.ClassManager.getByAlias('widget.' + portletConfig.xtype);
            if (!cls) {
                Phlexible.console.warn('Portlet widget.' + portletConfig.xtype + ' not found.');
                continue;
            }
            portletConfig.title = cls.prototype.title || this.noTitleText;
            portletConfig.description = cls.prototype.description || this.noDescriptionText;
            portletConfig.imageUrl = cls.prototype.imageUrl || '/bundles/phlexibledashboard/images/portlet-plain.png';
            portletConfig.hidden = false;
            col = false;
            pos = false;
            mode = 'closed';

            if (activePortlets[id]) {
                portletConfig.hidden = true;
                col  = parseInt(activePortlets[id].col, 10);
                pos  = parseInt(activePortlets[id].pos, 10);
                mode = activePortlets[id].mode || 'opened';
            }

            portletConfig.col = col;
            portletConfig.pos = pos;
            portletConfig.mode = mode;

            if (portletConfig.col !== false && portletConfig.pos !== false && portletConfig.mode !== 'closed') {
                if (portletConfig.col <= (cols - 1)) {
                    matrix[portletConfig.col].insert(portletConfig.pos, portletConfig);
                } else {
                    matrix[0].insert(portletConfig.pos, portletConfig);
                }
            }
        }
        */

        for (i = 0; i< cols; i++) {
            matrix[i].each(function(portletConfig) {
                this.addPortlet(portletConfig, true);
            }, this);
        }
    },

    reconfigure: function(cols, activePortlets) {
        this.getView().items.removeAll();
        this.configure(cols, activePortlets);
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
