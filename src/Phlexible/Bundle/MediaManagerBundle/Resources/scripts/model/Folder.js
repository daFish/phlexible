Ext.define('Phlexible.data.proxy.TreeRest', {
    extend: 'Ext.data.proxy.Rest',
    alias: 'proxy.rest-filter',

    buildUrl: function(request) {
        var me        = this,
            operation = request.getOperation(),
            records   = operation.getRecords(),
            record    = records ? records[0] : null,
            node      = operation.node || null,
            format    = me.getFormat(),
            url       = me.getUrl(request),
            params    = [],
            id;

        if (record && !record.phantom) {
            id = record.getId();
        } else {
            id = operation.getId();
        }

        if (format) {
            if (!url.match(me.periodRe)) {
                url += '.';
            }

            url += format;
        }

        if (node && this.parameters) {
            Ext.Object.each(this.parameters, function(key, config) {
                var value = node.get(config.field);
                if (config.skipIf && value === config.skipIf) {
                    return false;
                }
                if (config.emptyIf && value === config.emptyIf) {
                    value = '';
                }
                params.push(key + '=' + encodeURI(value));
            });
            url += '?' + params.join('&');
        }

        request.setUrl(url);

        return Ext.data.proxy.Rest.superclass.buildUrl.call(this, request);
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
        type: 'rest-filter',
        url: Phlexible.Router.generate('phlexible_api_mediamanager_get_folders'),
        parameters: {
            parentId: {field: 'id', emptyIf: 'root'}
        },
        reader: {
            type: 'json',
            rootProperty: 'folders'
        }
    }
});
