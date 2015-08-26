Ext.define('Phlexible.mediatype.model.MimeType', {
    extend: 'Ext.data.Model',

    entityName: 'MimeType',
    fields: [
        {name: 'type', type: 'string'},
        {name: 'subtype', type: 'string'},
        {name: 'parameters'},
        {name: 'mediatypeId', reference: {
            type: 'MediaType',
            inverse: 'mimetypes'
        }},
        {name: 'mimetype', calculate: function(data) {
            return data.type + '/' + data.subtype;
        }}
    ]
});
