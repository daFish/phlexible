Ext.provide('Phlexible.element.view.accordion.Instances');

Phlexible.element.view.accordion.Instances = Ext.extend(Ext.grid.GridPanel, {
    strings: Phlexible.elements.Strings,
    title: Phlexible.elements.Strings.instances,
    cls: 'p-elements-versions-accordion',
    iconCls: 'p-element-alias-icon',
    border: false,
    autoHeight: true,
    autoScroll: true,
    viewConfig: {
        deferEmptyText: false,
        emptyText: Phlexible.elements.Strings.no_versions_found,
        forceFit: true
    },

    initComponent: function () {
        this.store = new Ext.data.JsonStore({
            fields: [
                {name: 'id', type: 'int'},
                {name: 'instance_master', type: 'int'},
                {name: 'modify_time', type: 'date', dateFormat: 'Y-m-d H:i:s'},
                {name: 'icon', type: 'string'},
                {name: 'type', type: 'string'},
                {name: 'link'}
            ],
            id: 0
        });

        this.columns = [
            {
                header: this.strings.icon,
                dataIndex: 'icon',
                width: 25,
                renderer: function (s, meta, r) {
                    return '<img src=' + s + ' width="18" height="18" />';
                }
            },
            {
                header: this.strings.tid,
                dataIndex: 'id',
                width: 30,
                renderer: function (s, meta, r) {
                    if (r.data.instance_master) {
                        return '<b>' + s + '</b>';
                    }

                    return s;
                }
            },
            {
                header: this.strings.date,
                dataIndex: 'modify_time',
                width: 80,
                renderer: function (s) {
                    return s.format('Y-m-d H:i:s');
                }
            }
        ];

        this.on('rowdblclick', function (grid, index) {
            var r = this.store.getAt(index);
            if (r.data.link) {
                var menu = r.data.link;

                if (menu && menu.handler) {
                    var handler = menu.handler;
                    if (typeof handler == 'string') {
                        handler = Phlexible.evalClassString(handler);
                    }
                    handler(menu);
                }
            }
            else {
                if (r.data.type === 'treenode') {
                    this.fireEvent('loadElement', r.get('id'));
                }
                else {
                    this.fireEvent('loadTeaser', r.get('id'));
                }
            }
        }, this);

        Phlexible.element.view.accordion.Instances.superclass.initComponent.call(this);
    },

    load: function (element) {
        // Only show for full and part elements
        if (element.getElementtypeType() != 'full' && element.getElementtypeType() != 'part') {
            this.hide();
            return;
        }

        // Only show for elements with more then one instance
        if (!element.getInstances().length || element.getInstances().length < 2) {
            this.hide();
            return;
        }

        if (element.getElementtypeType() == 'part') {
            this.setIconClass('p-teasers-teaser_reference-icon');
        }
        else {
            this.setIconClass('p-element-alias-icon');
        }

        this.setTitle(this.strings.instances + ' [' + element.getInstances().length + ']');
        this.store.loadData(element.getInstances());

        var r = this.store.getById(element.getNodeId());
        if (r) {
            this.getSelectionModel().selectRecords([r]);
        }

        this.show();
    }
});

Ext.reg('elements-instancesaccordion', Phlexible.element.view.accordion.Instances);
