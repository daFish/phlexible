Ext.provide('Phlexible.tree.view.tab.Changes');

Ext.require('Phlexible.tree.view.Changes');

Phlexible.tree.view.tab.Changes = Ext.extend(Phlexible.tree.view.Changes, {
    initComponent: function () {
        Phlexible.tree.view.tab.Changes.superclass.initComponent.call(this);

        this.element.on('load', this.onLoadElement, this);

        this.store.baseParams = {
            filter_tid: null,
            filter_teaser_id: null
        };

        this.on('show', function () {
            if ((this.store.baseParams.tid != this.element.getNodeId()) || (this.store.baseParams.teaser_id != this.element.getTeaserId())) {
                this.onRealLoad(this.element.getEid(), this.element.getNodeId(), this.element.getTeaserId());
            }
        }, this);
    },

    onLoadElement: function (element) {
        if (!this.hidden) {
            this.onRealLoad(element.getEid(), element.getNodeId(), element.getTeaserId());
        }
    },

    onRealLoad: function (eid, nodeId, teaserId) {
        if (teaserId) {
            this.getColumnModel().setColumnHeader(2, this.strings.teaser_id);
            this.store.baseParams.filter_eid = '';
//            this.store.baseParams.filter_tid = '';
//            this.store.baseParams.filter_teaser_id = teaser_id;
        } else {
            this.getColumnModel().setColumnHeader(2, this.strings.tid);
            this.store.baseParams.filter_eid = eid;
//            this.store.baseParams.filter_tid = tid;
//            this.store.baseParams.filter_teaser_id = '';
        }

        this.store.load();
    }
});

Ext.reg('tree-tab-changes', Phlexible.tree.view.tab.Changes);
