Ext.define('Phlexible.mediacache.portlet.CacheStatus', {
    extend: 'Ext.dashboard.Panel',
    alias: 'widget.cache-status-portlet',

    iconCls: Phlexible.Icon.get('images-stack'),
    bodyPadding: 5,

    firstData: null,
    firstTs: null,
    imageUrl: '/bundles/phlexiblemediacache/images/portlet-cache-status.png',

    emptyText: '_emptyText',
    itemsText: '_itemsText',
    remainingText: '_remainingText',

    initComponent: function () {
        var itemsLeft = parseInt(this.item.data, 10);

        if (itemsLeft) {
            this.html = '<span id="media_cache_status">' + Ext.String.format(this.itemsLeftText, itemsLeft) + '</span>';
            this.firstData = itemsLeft;
            this.firstTs = new Date();
        }
        else {
            this.html = '<span id="media_cache_status">' + this.emptyText + '</span>';
        }

        this.callParent(arguments);
    },

    updateData: function (itemsLeft) {
        if (!this.rendered) {
            return;
        }

        itemsLeft = parseInt(itemsLeft, 10);

        if (itemsLeft) {
            if (this.firstData && this.firstData > itemsLeft) {
                var itemsDiff = this.firstData - itemsLeft;
                var dateDiff = (new Date() - this.firstTs) / 1000;
                var itemsPerSecond = itemsDiff / dateDiff;
                var itemsPerMinute = parseInt(itemsPerSecond * 60, 10);
                var secondsLeft = itemsLeft / itemsPerSecond;
                //var minutesLeft = parseInt(secondsLeft / 60, 10);

                this.body.first().update(String.format(this.remainingItemsText, itemsLeft, itemsPerMinute, Phlexible.Format.age(secondsLeft)));

                Ext.fly('media_cache_status').frame('#8db2e3', 1);
            }
            else {
                this.body.first().update(String.format(this.itemsLeftText, itemsLeft));
                this.firstData = itemsLeft;
                this.firstTs = new Date();
            }
        }
        else {
            this.body.first().update(String.format(this.emptyText));
            this.firstTs = null;
            this.firstData = null;
        }
    }
});