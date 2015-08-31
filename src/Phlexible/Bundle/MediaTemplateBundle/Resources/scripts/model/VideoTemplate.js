Ext.define('Phlexible.mediatemplate.model.VideoTemplate', {
    extend: 'Phlexible.mediatemplate.model.MediaTemplate',
    requires: [
        'Phlexible.mediatemplate.model.MediaTemplate'
    ],

    entityName: 'video',
    fields: [
        {name: "matchFormat", type: "boolean"},
        {name: "forWeb", type: "boolean"},
        {name: "format", type: "string"},
        {name: "deinterlace", type: "boolean"},
        {name: "resizeMethod", type: "string"},
        {name: "videoWidth", type: "integer"},
        {name: "videoHeight", type: "integer"},
        {name: "videoFormat", type: "string"},
        {name: "videoBitrate", type: "string"},
        {name: "videoFramerate", type: "string"},
        {name: "audioFormat", type: "string"},
        {name: "audioBitrate", type: "string"},
        {name: "audioSamplerate", type: "string"},
        {name: "audioSamplebits", type: "string"},
        {name: "audioChannels", type: "integer"}
    ]
});
