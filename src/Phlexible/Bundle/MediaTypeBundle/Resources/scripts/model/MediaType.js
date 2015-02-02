Ext.define('Phlexible.mediatype.model.MediaType', {
    extend: 'Ext.data.Model',

    fields: [
        {name: 'name', type: 'string'},
        {name: 'category', type: 'string'},
        {name: 'titles'},
        {name: 'mimetypes'},
        {name: 'icons'}
    ]
});
