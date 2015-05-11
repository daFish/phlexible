Ext.define('Phlexible.mediatype.model.MediaType', {
    extend: 'Ext.data.Model',
    requires: [
        'Phlexible.mediatype.model.MimeType'
    ],

    entityName: 'MediaType',
    idProperty: 'id',
    fields: [
        {name: 'name', type: 'string'},
        {name: 'category', type: 'string'},
        {name: 'svg', type: 'string'},
        {name: 'titles'},
        {name: 'mimetypes'},
        {name: 'icons'}
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
