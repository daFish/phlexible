Ext.namespace('Phlexible.mediatemplates');

Phlexible.mediatemplates.TemplateIcons = {
    image: Phlexible.Icon.get('image'),
    video: Phlexible.Icon.get('film'),
    audio: Phlexible.Icon.get('music'),
    pdf2swf: Phlexible.Icon.get('document-pdf')
};

Phlexible.mediatemplates.ImageMethods = [
    ['width', Phlexible.mediatemplates.Strings.method_width, Phlexible.mediatemplates.Strings.method_width_help],
    ['height', Phlexible.mediatemplates.Strings.method_height, Phlexible.mediatemplates.Strings.method_height_help],
    ['exact', Phlexible.mediatemplates.Strings.method_exact, Phlexible.mediatemplates.Strings.method_exact_help],
    ['exactFit', Phlexible.mediatemplates.Strings.method_exactFit, Phlexible.mediatemplates.Strings.method_exactFit_help],
    ['fit', Phlexible.mediatemplates.Strings.method_fit, Phlexible.mediatemplates.Strings.method_fit_help],
    ['crop', Phlexible.mediatemplates.Strings.method_crop, Phlexible.mediatemplates.Strings.method_crop_help]
];

Phlexible.mediatemplates.ImageScales = [
    ['updown', Phlexible.mediatemplates.Strings.scale_up_and_down],
    ['down', Phlexible.mediatemplates.Strings.scale_down],
    ['up', Phlexible.mediatemplates.Strings.scale_up]
];

Phlexible.mediatemplates.ImageFormats = [
    //['', Phlexible.mediatemplates.Strings.keep_format],
    ['gif', 'GIF'],
    ['jpg', 'JPG'],
    ['png', 'PNG'],
    ['tif', 'TIF'],
    ['bmp', 'BMP']
];

Phlexible.mediatemplates.ImageColorspaces = [
    ['', Phlexible.mediatemplates.Strings.keep_colorspace],
    ['rgb', Phlexible.mediatemplates.Strings.colorspace_rgb],
    ['cmyk', Phlexible.mediatemplates.Strings.colorspace_cmyk],
    ['gray', Phlexible.mediatemplates.Strings.colorspace_gray]
];

Phlexible.mediatemplates.ImageDepths = [
    ['', Phlexible.mediatemplates.Strings.keep_depth],
    ['8', Phlexible.mediatemplates.Strings.eight_bit_per_channel],
    ['16', Phlexible.mediatemplates.Strings.sixteen_bit_per_channel]
];

Phlexible.mediatemplates.ImageTiffCompressions = [
    ['none', Phlexible.mediatemplates.Strings.tiffcompression_none],
    ['zip', Phlexible.mediatemplates.Strings.tiffcompression_zip],
    ['lzw', Phlexible.mediatemplates.Strings.tiffcompression_lzw]
];

Phlexible.mediatemplates.ImageCompressions = [
    ['0', '0 - ' + Phlexible.mediatemplates.Strings.compression_huffman],
    ['1', '1 - ' + Phlexible.mediatemplates.Strings.compression_fastest],
    ['2', '2'],
    ['3', '3'],
    ['4', '4'],
    ['5', '5'],
    ['6', '6'],
    ['7', '7'],
    ['8', '8'],
    ['9', '9 - ' + Phlexible.mediatemplates.Strings.compression_best]
];

Phlexible.mediatemplates.ImageFilterTypes = [
    ['0', 'None'],
    ['1', 'Sub'],
    ['2', 'Up'],
    ['3', 'Average'],
    ['4', 'Paeth'],
    ['5', 'Adaptive filtering']
];

Phlexible.mediatemplates.VideoFormats = [
    ['', Phlexible.mediatemplates.Strings.keep_format],
    ['flv', 'FLV'],
    ['mp4', 'MP4'],
    ['ogg', 'OGG'],
    ['wmv', 'WMV'],
    ['wmv3', 'WMV3'],
    ['webm', 'WEBM'],
    ['3gp', '3GP']
];

Phlexible.mediatemplates.VideoFormatsWeb = [
    ['flv', 'FLV'],
    ['mp4', 'MP4'],
    ['ogg', 'OGG']
];

Phlexible.mediatemplates.VideoBitrates = [
    ['', Phlexible.mediatemplates.Strings.keep_bitrate],
    ['300k', '300k'],
    ['500k', '500k'],
    ['800k', '800k'],
    ['1000k', '1000k'],
    ['2000k', '2000k']
];

Phlexible.mediatemplates.VideoFramerates = [
    ['', Phlexible.mediatemplates.Strings.keep_framerate],
    ['5', '5'],
    ['10', '10'],
    ['15', '15'],
    ['20', '20'],
    ['25', '25']
];

Phlexible.mediatemplates.AudioFormats = [
    ['', Phlexible.mediatemplates.Strings.keep_format],
    ['mp3', 'MP3'],
    ['flac', 'FLAC'],
    ['vorbis', 'VORBIS']
];

Phlexible.mediatemplates.AudioFormatsWeb = [
    ['mp3', 'MP3']
];

Phlexible.mediatemplates.AudioBitrates = [
    ['', Phlexible.mediatemplates.Strings.keep_bitrate],
    ['32k', '32k'],
    ['64k', '64k'],
    ['96k', '96k'],
    ['128k', '128k'],
    ['192k', '192k'],
    ['256k', '256k'],
    ['320k', '320k']
];

Phlexible.mediatemplates.AudioSamplerates = [
    ['', Phlexible.mediatemplates.Strings.keep_samplerate],
    ['11025', '11025 Hz'],
    ['22050', '22050 Hz'],
    ['44100', '44100 Hz']
];

Phlexible.mediatemplates.AudioSamplebits = [
    ['', Phlexible.mediatemplates.Strings.keep_samplebits],
    ['8', '8 bit'],
    ['16', '16 bit'],
    ['32', '32 bit']
];

Phlexible.mediatemplates.AudioChannels = [
    ['', Phlexible.mediatemplates.Strings.keep_channels],
    ['0', Phlexible.mediatemplates.Strings.channels_no],
    ['1', Phlexible.mediatemplates.Strings.channels_mono],
    ['2', Phlexible.mediatemplates.Strings.channels_stereo]
];
