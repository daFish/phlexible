Ext.define('Phlexible.mediamanager.portlet.LatestFiles', {
    extend: 'Portal.view.Portlet',
    alias: 'widget.mediamanager-latest-files-portlet',

    title: '_LatestFiles',
    iconCls: 'p-mediamanager-portlet-icon',
    bodyStyle: 'padding: 5px 5px 5px 5px',

    type: 'small',

    noRecentFilesText: '_noRecentFilesText',

    initComponent: function () {
        if (this.record.data.settings.style) {
            this.type = this.record.data.settings.style;
        }

        var tpl;
        switch (this.type) {
            case 'big':
                this.extraCls = 'mediamanager-portlet mediamanager-portlet-big';
                tpl = this.createLatestFilesBigTemplate();
                break;

            case 'list':
                this.extraCls = 'mediamanager-portlet mediamanager-portlet-list';
                tpl = this.createLatestFilesListTemplate();
                break;

            default:
                this.extraCls = 'mediamanager-portlet mediamanager-portlet-small';
                tpl = this.createLatestFilesSmallTemplate();
                break;
        }

        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.mediamanager.model.LatestFile',
            id: 'id',
            sorters: [{
                property: 'time',
                direction: 'DESC'
            }]
        });

        var data = this.record.get('data');
        if (data) {
            Ext.each(data, function (item) {
                item.time = new Date(item.time * 1000);
                this.store.add(new Phlexible.mediamanager.model.LatestFile(item, item.id));
            }, this);
        }

        this.items = [
            {
                xtype: 'dataview',
                itemSelector: 'div.thumb-wrap',
                overItemCls: 'thumb-wrap-over',
                style: 'overflow: auto',
                singleSelect: true,
                emptyText: this.noRecentFilesText,
                deferEmptyText: false,
                autoHeight: true,
                store: this.store,
                tpl: tpl,
                listeners: {
                    click: function (c, index, node) {
                        var r = this.store.getAt(index);

                        Phlexible.Frame.loadPanel(
                            'Media_manager_MediamanagerPanel',
                            Phlexible.mediamanager.MediamanagerPanel,
                            {
                                startFileId: r.get('fileId'),
                                startFolderPath: r.get('folderPath')
                            }
                        );
                    },
                    scope: this
                }
            }
        ];

        this.callParent(arguments);
    },

    updateData: function (data) {
        var latestFilesMap = [];

        for (var i = 0; i < data.length; i++) {
            var row = data[i];
            latestFilesMap.push(row.id);
            var r = this.store.getById(row.id);
            if (!r) {
                row.time = new Date(row.time * 1000);
                this.store.insert(0, new Phlexible.mediamanager.portlet.LatestFilesRecord(row, row.id));

                Ext.fly('media_last_' + row.id).frame('#8db2e3', 1);
            }
            else {
                var needUpdate = false;
                for (var j in row.cache) {
                    if (row.cache[j] != r.data.cache[j]) {
                        needUpdate = true;
                        break;
                    }
                }
                if (needUpdate) {
                    r.beginEdit();
                    r.set('cache', null);
                    r.set('cache', row.cache);
                    r.endEdit();
                }
            }
        }

        for (var i = this.store.getCount() - 1; i >= 0; i--) {
            var r = this.store.getAt(i);
            if (latestFilesMap.indexOf(r.id) == -1) {
                this.store.remove(r);
            }
        }

        this.store.sort('time', 'DESC');
        this.getComponent(0).refresh();
    },

    createLatestFilesBigTemplate: function() {
        return new Ext.XTemplate(
            '<tpl for=".">',
            '<div class="thumb-wrap" id="media_last_{id}">',
            '<div class="thumb"><img src="{[Phlexible.Router.generate(\"mediamanager_media\", {fileId: values.fileId, templateKey: \"_mm_large\", fileVersion: values.fileVersion})]}<tpl if="!cache._mm_large">?waiting</tpl><tpl if="cache._mm_large">?{[values.cache._mm_large]}</tpl>" width="96" height="96" title="{title}" /></div>',
            '<span>{[values.title.shorten(20)]}</span>',
            '<span class="thumb-date">{time:date("Y-m-d H:i:s")}</span>',
            '</div>',
            '</tpl>',
            '<div class="x-clear"></div>'
        );
    },

    createLatestFilesSmallTemplate: function() {
        return new Ext.XTemplate(
            '<tpl for=".">',
            '<div class="thumb-wrap" id="media_last_{id}">',
            '<div class="thumb"><img src="{[Phlexible.Router.generate(\"mediamanager_media\", {fileId: values.fileId, templateKey: \"_mm_small\", fileVersion: values.fileVersion})]}<tpl if="!cache._mm_small">?waiting</tpl><tpl if="cache._mm_small">?{[values.cache._mm_small]}</tpl>" width="32" height="32" /></div>',
            '<div class="text">',
            '<span>{[values.title.shorten(20)]}</span>',
            '<span class="thumb-type">{[Phlexible.documenttypes.DocumentTypes.getText(values.mediaType)]}</span>',
            '<span class="thumb-date">{time:date("Y-m-d H:i:s")}</span>',
            '</div>',
            '<div class="x-clear"></div>',
            '</div>',
            '</tpl>',
            '<div class="x-clear"></div>'
        );
    },

    createLatestFilesListTemplate: function() {
        return new Ext.XTemplate(
            '<tpl for=".">',
            '<div class="thumb-wrap" id="media_last_{id}">',
            '<div class="thumb {[Phlexible.mediamanager.DocumentTypes.getClass(values.mediaType)]}-small"></div>',
            '<div class="text">',
            '<span>{[values.title.shorten(60)]}</span>',
            '<br />',
            '<span class="thumb-date">{time:date("Y-m-d H:i:s")}, {[Phlexible.mediamanager.DocumentTypes.getText(values.mediaType)]}</span>',
            '</div>',
            '<div class="x-clear"></div>',
            '</div>',
            '</tpl>'
        );
    }
});
