Ext.define('Phlexible.mediatype.model.MediaType', {
    extend: 'Ext.data.Model',
    xequires: [
        'Phlexible.mediatype.model.MimeType'
    ],

    entityName: 'MediaType',
    fields: [
        {name: 'name', type: 'string'},
        {name: 'category', type: 'string'},
        {name: 'mimetypes'},
        {name: 'attributes'},
        {name: 'icon', calculate: function(data) {
            return data.attributes.icon || 'document';
        }}
    ],
    proxy: {
        type: 'rest',
        url: Phlexible.Router.generate('phlexible_api_mediatype_get_mediatypes'),
        simpleSortMode: true,
        reader: {
            type: 'json',
            rootProperty: 'mediatypes',
            totalProperty: 'count'
        }
    }
});
