Ext.define('Phlexible.mediatemplate.model.ImageTemplate', {
    extend: 'Phlexible.mediatemplate.model.MediaTemplate',
    requires: [
        'Phlexible.mediatemplate.model.MediaTemplate'
    ],

    entityName: 'image',
    fields: [
        {name: "width", type: "integer"},
        {name: "height", type: "integer"},
        {name: "method", type: "string"},
        {name: "scale", type: "string"},
        {name: "forWeb", type: "boolean"},
        {name: "format", type: "string"},
        {name: "colorspace", type: "string"},
        {name: "tiffCompression", type: "string"},
        {name: "depth", type: "string"},
        {name: "quality", type: "integer"},
        {name: "backgroundcolor", type: "string"},
        {name: "compression", type: "integer", calculate: function(data) {
            return data.quality ? Math.floor(data.quality / 10) : null;
        }},
        {name: "filtertype", type: "integer", calculate: function(data) {
            return data.quality ? data.quality % 10 : null;
        }}
    ]
});
