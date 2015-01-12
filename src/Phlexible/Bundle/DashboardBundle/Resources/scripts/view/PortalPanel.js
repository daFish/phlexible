/**
 * Portal panel
 */
Ext.define('Phlexible.dashboard.view.PortalPanel', {
    extend: 'Portal.view.PortalPanel',
    alias: 'widget.dashboard-portalpanel',

    store: Ext.create('Phlexible.dashboard.store.Portlets'),
    cls: 'p-dashboard-portal-panel',
    border: false,
    cols: 3,
    panels: {},
    ui: 'nope',

    /**
     * @event portletAdd
     */
    /**
     * @event portletClose
     */
    /**
     * @event portletCollapse
     */
    /**
     * @event portletExpand
     */

    /**
     *
     */
    initComponent: function() {
        var items = [],
            poller = Phlexible.App.getPoller();

        if (poller) {
            poller.on('message', this.processMessage, this);
        }

        for (i=0; i<this.cols; i+=1) {
            items.push({
                id: 'col' + i,
                col: i,
                columnWidth: 1/this.cols,
                padding: 10
                //,items: [{title: 'Column' + (i+1), html: 'test'}]
            });
        }

        this.items = items;

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

        this.callParent(arguments);
    },

    updatePanels: function() {
        for(var i=0; i<this.store.getCount(); i++){
            var r = this.store.getAt(i);
            this.addRecordPanel(r);
        }

        this.doLayout();
    },

    getCol: function(pos) {
        return this.items.get(pos);
    },

    getBestCol: function() {
        var childs = {};
        var best;
        var max = 999;

        for(i=0; i<this.items.getCount(); i++) {
            var item = this.items.get(i);
            if (!item.items.getCount()) {
                return item;
            }
            var cnt = item.getSize().height;
            if (cnt < max) {
                max = cnt;
                best = item;
            }
        }

        return best;
    },

    addPortlet: function(item, skipEvent) {
        var col;
        if (item.col !== false && item.col < this.cols) {
            col = this.getCol(item.col);
        } else {
            col = this.getBestCol();
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

            this.doLayout();

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
    }
});
