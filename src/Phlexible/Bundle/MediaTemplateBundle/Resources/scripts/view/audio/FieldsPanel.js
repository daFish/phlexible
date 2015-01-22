Ext.define('Phlexible.mediatemplate.view.audio.FieldsPanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.mediatemplates-audio-fields',

    title: '_FieldsPanel',
    iconCls: Phlexible.mediatemplate.TemplateIcons.audio,
    bodyPadding: 5,
    border: false,
    autoScroll: true,

    keepBitrateText: '_keepBitrateText',
    bitrateText: '_bitrateText',
    bitrateHelpText: '_bitrateHelpText',
    keepSamplerateText: '_keepSamplerateText',
    samplerateText: '_samplerateText',
    samplerateHelpText: '_samplerateHelpText',
    keepSamplebitsText: '_keepSamplebitsText',
    samplebitsText: '_samplebitsText',
    samplebitsHelpText: '_samplebitsHelpText',
    channelsText: '_channelsText',
    channelsHelpText: '_channelsHelpText',
    keepChannelsText: '_keepChannelsText',
    noChannelsText: '_noChannelsText',
    channelsMonoText: '_channelsMonoText',
    channelsStereoText: '_channelsStereoText',

    /*

     Phlexible.mediatemplate.AudioFormats = [
     ['', this.keepFormatText],
     ['mp3', 'MP3'],
     ['flac', 'FLAC'],
     ['vorbis', 'VORBIS']
     ];

     Phlexible.mediatemplate.AudioFormatsWeb = [
     ['mp3', 'MP3']
     ];

     */
    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'combo',
                store: Ext.create('Ext.data.Store', {
                    fields: ['id', 'bitrate'],
                    data: [
                        ['', this.keepBitrateText],
                        ['32k', '32k'],
                        ['64k', '64k'],
                        ['96k', '96k'],
                        ['128k', '128k'],
                        ['192k', '192k'],
                        ['256k', '256k'],
                        ['320k', '320k']
                    ]
                }),
                valueField: 'id',
                displayField: 'bitrate',
                typeAhead: false,
                mode: 'local',
                triggerAction: 'all',
                value: '',
                editable: false,
                fieldLabel: this.bitrateText,
                name: 'audio_bitrate',
                width: 280,
                listWidth: 280,
                helpText: this.bitrateHelpText
            },
            {
                xtype: 'combo',
                store: Ext.create('Ext.data.Store', {
                    fields: ['id', 'samplerate'],
                    data: [
                        ['', this.keepSamplerateText],
                        ['11025', '11025 Hz'],
                        ['22050', '22050 Hz'],
                        ['44100', '44100 Hz']
                    ]
                }),
                valueField: 'id',
                displayField: 'samplerate',
                typeAhead: false,
                mode: 'local',
                triggerAction: 'all',
                value: '',
                editable: false,
                fieldLabel: this.samplerateText,
                name: 'audio_samplerate',
                width: 280,
                listWidth: 280,
                helpText: this.samplerateHelpText
            },
            {
                xtype: 'combo',
                store: Ext.create('Ext.data.Store', {
                    fields: ['id', 'samplebits'],
                    data: [
                        ['', this.keepSamplebitsText],
                        ['8', '8 bit'],
                        ['16', '16 bit'],
                        ['32', '32 bit']
                    ]
                }),
                valueField: 'id',
                displayField: 'samplebits',
                typeAhead: false,
                mode: 'local',
                triggerAction: 'all',
                value: '',
                editable: false,
                fieldLabel: this.samplebitsText,
                name: 'audio_samplebits',
                width: 280,
                listWidth: 280,
                helpText: this.samplebitsHelpText
            },
            {
                xtype: 'combo',
                store: Ext.create('Ext.data.Store', {
                    fields: ['id', 'channels'],
                    data: [
                        ['', this.keepChannelsText],
                        ['0', this.noChannelsText],
                        ['1', this.channelsMonoText],
                        ['2', this.channelsStereoText]
                    ]
                }),
                valueField: 'id',
                displayField: 'channels',
                typeAhead: false,
                mode: 'local',
                triggerAction: 'all',
                value: '',
                editable: false,
                fieldLabel: this.channelsText,
                name: 'audio_channels',
                width: 280,
                listWidth: 280,
                helpText: this.channelsHelpText
            }
        ]
    }
});
