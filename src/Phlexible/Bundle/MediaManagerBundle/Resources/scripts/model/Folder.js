Ext.define('Ext.data.proxy.TreeRest', {
    extend: 'Ext.data.proxy.Rest',
    alias: 'proxy.treerest',
    buildUrl: function(request) {
        var me        = this,
            operation = request.getOperation(),
            records   = operation.getRecords(),
            record    = records ? records[0] : null,
            format    = me.getFormat(),
            url       = me.getUrl(request),
            id, params;

        if (record && !record.phantom) {
            id = record.getId();
        } else {
            id = operation.getId();
        }

        if (id !== 'root' && me.getAppendId() && me.isValidId(id)) {
            if (!url.match(me.slashRe)) {
                url += '/';
            }

            url += encodeURIComponent(id);
            url += '/folders';
            params = request.getParams();
            if (params) {
                delete params[me.getIdParam()];
            }
        }

        if (format) {
            if (!url.match(me.periodRe)) {
                url += '.';
            }

            url += format;
        }

        if (me.getNoCache()) {
            url = Ext.urlAppend(url, Ext.String.format("{0}={1}", me.getCacheString(), Ext.Date.now()));
        }

        request.setUrl(url);

        return url;
    }
});

Ext.define('Phlexible.mediamanager.model.Folder', {
    extend: 'Ext.data.TreeModel',
    entityName: 'Folder',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'name', type: 'string'},
        {name: 'path', type: 'string'},
        {name: 'volumeId', type: 'string'},
        {name: 'createdAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'createdBy', type: 'string'},
        {name: 'modifiedAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'modifiedBy', type: 'string'},
        {name: 'usageStatus', type: 'int'},
        {name: 'usedIn'},
        {name: 'attributes'},
        {name: 'rights'}
    ],
    proxy: {
        type: 'treerest',
        url: Phlexible.Router.generate('phlexible_api_mediamanager_get_folders'),
        reader: {
            type: 'json',
            rootProperty: 'folders'
        }
    },
    root: {
        id: 'root',
        text: 'root',
        expanded: true,
        iconCls: Phlexible.Icon.get('folder')
    }
});
