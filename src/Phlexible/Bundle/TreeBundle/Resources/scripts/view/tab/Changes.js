Ext.provide('Phlexible.tree.view.tab.Changes');

Ext.require('Phlexible.tree.view.Changes');

Phlexible.tree.view.tab.Changes = Ext.extend(Phlexible.tree.view.Changes, {
    initComponent: function () {
        Phlexible.tree.view.tab.Changes.superclass.initComponent.call(this);

        this.element.on('load', this.onLoadElement, this);

        this.store.baseParams = {
            nodeId: null
        };

        this.on('show', function () {
            if ((this.store.baseParams.tid != this.element.getNodeId()) || (this.store.baseParams.teaser_id != this.element.getTeaserId())) {
                this.onRealLoad(this.element.getNodeId());
            }
        }, this);
    },

    onLoadElement: function (element) {
        if (!this.hidden) {
            this.onRealLoad(element.getNodeId());
        }
    },

    onRealLoad: function (nodeId) {
        this.store.baseParams.nodeId = nodeId;

        this.store.load();
    }
});

Ext.reg('tree-tab-changes', Phlexible.tree.view.tab.Changes);
