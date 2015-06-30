Ext.define('Phlexible.mediamanager.portlet.LatestFiles', {
    extend: 'Ext.dashboard.Panel',
    requires: ['Phlexible.mediamanager.model.LatestFile'],
    alias: 'widget.mediamanager-latest-files-portlet',

    iconCls: Phlexible.Icon.get('images'),
    bodyPadding: 5,

    type: 'tile',
    imageUrl: '/bundles/phlexiblemediamanager/images/portlet-latest-files.png',

    noRecentFilesText: '_noRecentFilesText',

    initComponent: function () {
        if (this.item.settings && this.item.settings.style) {
            this.type = this.item.settings.style;
        }

        var tpl;
        switch (this.type) {
            case 'big':
                this.cls = 'mediamanager-portlet mediamanager-portlet-big';
                tpl = this.createLatestFilesBigTemplate();
                break;

            case 'list':
                this.cls = 'mediamanager-portlet mediamanager-portlet-list';
                tpl = this.createLatestFilesListTemplate();
                break;

            default:
                this.cls = 'mediamanager-portlet mediamanager-portlet-tile';
                tpl = this.createLatestFilesSmallTemplate();
                break;
        }

        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.mediamanager.model.LatestFile',
            id: 'id',
            sorters: [{
                property: 'time',
                direction: 'DESC'
            }],
            data: this.item.data
        });

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
        var latestFilesMap = [],
            i, j, row, record, needUpdate = false;

        for (i = 0; i < data.length; i++) {
            row = data[i];
            latestFilesMap.push(row.id);
            record = this.store.findRecord('id', row.id);
            if (!record) {
                this.store.insert(0, Ext.create('Phlexible.mediamanager.model.LatestFile', row));
            }
            else {
                for (j in row.cache) {
                    if (row.cache[j] !== record.data.cache[j]) {
                        needUpdate = true;
                        break;
                    }
                }
                if (needUpdate) {
                    record.beginEdit();
                    record.set('cache', null);
                    record.set('cache', row.cache);
                    record.endEdit();
                }
            }
        }

        for (i = this.store.getCount() - 1; i >= 0; i--) {
            record = this.store.getAt(i);
            if (latestFilesMap.indexOf(record.id) === -1) {
                this.store.remove(record);
            }
        }

        this.store.sort('time', 'DESC');
        this.getComponent(0).refresh();
    },

    createLatestFilesBigTemplate: function() {
        return new Ext.XTemplate(
            '<tpl for=".">',
            '<div class="thumb-wrap" id="media_last_{id}">',
            '<div class="thumb"><img src="{[Phlexible.Router.generate(\"phlexible_mediamanager_media\", {fileId: values.fileId, templateKey: \"_mm_large\", fileVersion: values.fileVersion})]}<tpl if="!cache._mm_large">?waiting</tpl><tpl if="cache._mm_large">?{[values.cache._mm_large]}</tpl>" width="96" height="96" title="{name}" /></div>',
            '<span>{[Ext.String.ellipsis(values.name, 20)]}</span>',
            '<span class="thumb-date">{createdAt:date("Y-m-d H:i:s")}</span>',
            '</div>',
            '</tpl>',
            '<div class="x-clear"></div>'
        );
    },

    createLatestFilesSmallTemplate: function() {
        return new Ext.XTemplate(
            '<tpl for=".">',
            '<div class="thumb-wrap" id="media_last_{id}">',
            '<div class="thumb"><img src="{[Phlexible.Router.generate(\"phlexible_mediamanager_media\", {fileId: values.fileId, templateKey: \"_mm_medium\", fileVersion: values.fileVersion})]}<tpl if="!cache._mm_small">?waiting</tpl><tpl if="cache._mm_small">?{[values.cache._mm_small]}</tpl>" width="48" height="48" /></div>',
            '<div class="text">',
            '<span>{[Ext.String.ellipsis(values.name, 20)]}</span>',
            '<span class="thumb-type">{[Phlexible.documenttypes.DocumentTypes.getText(values.mediaType)]}</span>',
            '<span class="thumb-date">{createdAt:date("Y-m-d H:i:s")}</span>',
            '</div>',
            '<div class="x-clear"></div>',
            '</div>',
            '</tpl>',
            '<div class="x-clear"></div>'
        );
    },

    createLatestFilesListTemplate: function() {
        return new Ext.XTemplate(
            '<table>',
            '<tpl for=".">',
            '<tr class="thumb-wrap" id="media_last_{id}">',
            '<td class="thumb {[Phlexible.documenttypes.DocumentTypes.getClass(values.mediaType)]}-small"></td>',
            '<td class="text">{[Ext.String.ellipsis(values.name, 60)]}</td>',
            '<td class="thumb-date">{createdAt:date("Y-m-d H:i:s")}</td>',
            '<td class="thumb-type">{[Phlexible.documenttypes.DocumentTypes.getText(values.mediaType)]}</td>',
            '</tr>',
            '</tpl>',
            '</table>'
        );
    }
});
