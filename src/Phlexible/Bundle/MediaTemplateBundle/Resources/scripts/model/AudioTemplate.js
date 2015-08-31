Ext.define('Phlexible.mediatemplate.model.AudioTemplate', {
    extend: 'Phlexible.mediatemplate.model.MediaTemplate',
    requires: [
        'Phlexible.mediatemplate.model.MediaTemplate'
    ],

    entityName: 'audio',
    fields: [
        {name: "audioFormat", type: "string"},
        {name: "audioBitrate", type: "string"},
        {name: "audioSamplerate", type: "string"},
        {name: "audioSamplebits", type: "string"},
        {name: "audioChannels", type: "integer"}
    ]
});
