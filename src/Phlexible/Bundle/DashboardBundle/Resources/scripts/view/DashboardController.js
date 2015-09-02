/**
 * Dashboard
 */
Ext.define('Phlexible.dashboard.view.DashboardController', {
    extend: 'Ext.app.ViewController',

    requires: [
        'Phlexible.dashboard.infobar.Welcome',
        'Phlexible.dashboard.window.ListWindow',
        'Phlexible.dashboard.store.Portlets',
        'Phlexible.dashboard.model.Portlet',
        'Ext.DomHelper'
    ],

    alias: 'controller.dashboard.dashboard',

    store: Ext.create('Phlexible.dashboard.store.Portlets'),

    noTitleText: '_noTitleText',
    noDescriptionText: '_noDescriptionText',

    init: function() {
        Phlexible.Logger.debug('DashboardController.init()');

        Phlexible.dashboard.Portlets = Ext.create('Phlexible.dashboard.store.Portlets');
        Phlexible.Storage.each('portlet', function(item) {
            Phlexible.dashboard.Portlets.add(Ext.create('Phlexible.dashboard.model.Portlet', item));
        });

        Phlexible.App.getPoller().on('message', this.processMessage, this);

        var activePortlets = [{
            id: 'column',
            children: [{
                id: 'load-portlet'
            }]
        }];
        if (Phlexible.User.getProperty('dashboard.portlets')) {
            activePortlets = Phlexible.User.getProperty('dashboard.portlets');
        }

        this.configure(activePortlets);

        this.initMyTasks();
        this.initMyListeners();

        this.callParent(arguments);
    },

    initMyTasks: function() {
        this.saveTask = new Ext.util.DelayedTask(this.doSave, this);
    },

    initMyListeners: function() {
        this.getView().on({
            add: this.save,
            remove: this.save,
            render: function() {
                Ext.DomHelper.append(this.getView().getEl(), {
                    tag: 'img',
                    src: '/bundles/phlexiblegui/images/watermark.gif',
                    cls: 'p-dashboard-watermark'
                });

                if (Phlexible.User.isGranted('ROLE_ADMIN')) {
                    Ext.DomHelper.append(this.getView().getEl(), {
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

    processMessage: function(event){
        if (!Ext.isObject(event) && event.type == "dashboard") {
            return;
        }

        this.getView().cascade(function(c) {
            if (event.data[c.portletId]) {
                c.updateData(event.data[c.portletId]);
            }
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

    getBestColumn: function() {
        var max = 999,
            bestColumn;

        this.getView().items.each(function(column) {
            if (column.$className !== 'Ext.dashboard.Column') {
                return;
            }
            if (!column.items.length) {
                bestColumn = column;
                return false;
            }
            var height = column.getSize().height;
            if (height < max) {
                max = height;
                bestColumn = column;
            }
        });

        return bestColumn;
    },

    addPortlet: function(item) {
        var column = this.getBestColumn(),
            config;

        if (column && item.xtype) {
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

            config = this.createPortletConfig(item.id);
            config.tools = tools;
            config.plugins = plugins;

            column.add(config);
        }
    },

    createPortletConfig: function(id) {
        Phlexible.dashboard.Portlets.clearFilter();

        var portlet = Phlexible.dashboard.Portlets.findRecord('id', id),
            config;

        if (portlet) {
            config = {
                portletId: id,
                data: {},
                xtype: portlet.data.xtype,
                listeners: {
                    close: function () {
                        this.save();
                        Phlexible.dashboard.Portlets.findRecord('id', id).set('hidden', false);
                    },
                    scope: this
                }
            };
            portlet.set('hidden', true);
        }

        return config;
    },

    recurseConfigs: function(items) {
        var configs = [], count = items.length;

        Ext.each(items, function(item) {
            var config;
            if (item.id === 'column') {
                config = {
                    xtype: 'dashboard-column',
                    columnWidth: 1 / count,
                    items: this.recurseConfigs(item.children),
                };
                configs.push(config);
            } else {
                config = this.createPortletConfig(item.id);
                if (config) {
                    configs.push(config);
                }
            }
        }, this);

        return configs;
    },

    configure: function(activePortlets) {
        var configs = this.recurseConfigs(activePortlets);

        this.getView().add(configs);

        this.getView().cascade(function(c) {
            if (c.$className !== 'Ext.dashboard.Column') {
                return;
            }
            c.on({
                add: this.save,
                remove: this.save,
                scope: this
            });
        }, this);
    },

    save: function() {
        this.saveTask.cancel();
        this.saveTask.delay(2000);
    },

    doSave: function() {
        this.saveTask.cancel();

        var data = this.getSaveData();

        Phlexible.User.setProperty('dashboard.portlets', data);
        Phlexible.User.commit();
    },

    getSaveData: function() {
        return this.recurseItems(this.getView().items);
    },

    recurseItems: function(items) {
        var data = [];

        items.each(function(item) {
            if (item.$className === 'Ext.layout.container.ColumnSplitter') {
                return;
            } else if (item.$className === 'Ext.dashboard.Column') {
                var children = this.recurseItems(item.items);
                if (!children.length) {
                    return;
                }
                data.push({
                    id: 'column',
                    children: this.recurseItems(item.items)
                });
            } else {
                data.push({
                    id: item.portletId,
                    mode: item.getCollapsed() ? 'collapsed' : 'expanded'
                });
            }
        }, this);

        return data;
    }
});
