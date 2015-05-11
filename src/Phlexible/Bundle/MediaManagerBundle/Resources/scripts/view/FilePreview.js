Ext.define('Phlexible.mediamanager.view.FilePreview', {
    extend: 'Ext.panel.Panel',
    xtype: 'mediamanager.file-preview',

    title: '_FilePreviewPanel',
    cls: 'p-mediamanager-preview-panel',
    height: 270,
    padding: 5,

    fileId: null,
    fileVersion: null,
    fileName: null,
    mediaType: null,
    mediaCategory: null,
    cache: null,

    noPreviewAvailableText: '_noPreviewAvailableText',

    // private
    initComponent: function () {
        if (this.fileId && this.fileVersion && this.fileName && this.mediaType && this.mediaCategory && this.cache) {
            this.html = this.getHtml(this.fileId, this.fileVersion, this.fileName, this.mediaType, this.mediaCategory, this.cache);
        }
        else {
            this.html = this.createNoPreview();
        }

        this.callParent(arguments);
    },

    loadRecord: function (r) {
        this.load(r.get('id'), r.get('version'), r.get('name'), r.get('mediaType'), r.get('mediaCategory'), r.get('cache'));
    },

    load: function (fileId, fileVersion, fileName, mediaType, mediaCategory, cache) {
        if (this.fileId != fileId || this.fileVersion != fileVersion) {
            this.fileId = fileId;
            this.fileVersion = fileVersion;
            this.fileName = fileName;
            this.mediaType = mediaType;
            this.mediaCategory = mediaCategory;
            this.cache = cache;

            this.body.update('');
            this.body.insertFirst(this.getHtml(fileId, fileVersion, fileName, mediaType, mediaCategory, cache));
        }
    },

    getHtml: function (fileId, fileVersion, fileName, mediaType, mediaCategory, cache) {
        switch (mediaCategory.toUpperCase()) {
            case Phlexible.mediamanager.AUDIO:
                return this.createAudioPlayer(256, 256, fileId, fileVersion, fileName, cache);
                break;

            case Phlexible.mediamanager.VIDEO:
                return this.createVideoPlayer(256, 256, fileId, fileVersion, fileName, cache);
                break;

            case Phlexible.mediamanager.FLASH:
                return this.createFlashPlayer(256, 256, fileId, fileVersion, fileName, cache);
                break;

            case Phlexible.mediamanager.IMAGE:
            default:
                return this.createImage(256, 256, fileId, fileVersion, fileName, cache);
        }
    },

    getLink: function (fileId, templateKey, fileVersion, cache) {
        if (cache && templateKey && cache[templateKey]) {
            return cache[templateKey];
        }

        var parameters = {
            fileId: fileId,
            templateKey: templateKey
        };
        if (fileVersion) {
            parameters.fileVersion = fileVersion;
        }
        if (cache && cache[templateKey]) {
            parameters.cache = cache[templateKey];
        } else if (cache !== false) {
            parameters.waiting = 1;
        }

        return Phlexible.Router.generate('mediamanager_media', parameters);
    },

    empty: function () {
        this.body.update('');
        this.body.insertFirst(this.createNoPreview());
    },

    createNoPreview: function () {
        return {
            tag: 'table',
            border: 0,
            width: '100%',
            height: '100%',
            children: [
                {
                    tag: 'tr',
                    children: [
                        {
                            tag: 'td',
                            align: 'center',
                            valign: 'middle',
                            html: this.noPreviewAvailableText
                        }
                    ]
                }
            ]
        };
        return '<table border="0" width="100%" height="100%"><tr><td align="center" valign="middle">' + this.noPreviewAvailableText + '</td></tr></table>';
    },

    createFlashPlayer: function (width, height, fileId, fileVersion, fileName, cache) {
        var link = this.getLink(fileId, fileVersion) + '/' + fileName + '.swf';

        return {
            tag: 'embed',
            src: link,
            width: width,
            height: height,
            allowfullscreen: 'true',
            allowscriptaccess: 'always',
            wmode: 'transparent',
            flashvars: link
        };
        return '<embed src="' + link + '" width="' + width + '" height="' + height + '" allowfullscreen="true" allowscriptaccess="always" wmode="transparent" flashvars="' + link + '" />';
    },

    createAudioPlayer: function (width, height, fileId, fileVersion, fileName, cache) {
        if (!cache._mm_preview_audio || cache._mm_preview_audio.substr(0, 2) !== 'ok') {
            return this.createImage(256, 256, fileId, fileVersion, fileName, cache);
        }

        var image = this.getLink(fileId, '_mm_preview_player', fileVersion, cache);
        var audio = this.getLink(fileId, '_mm_preview_audio', fileVersion, false) + '/name/' + fileName + '.mp3';
        //var link = '&file=' + audio + '&image=' + image + '&height=' + height + '&width=' + width + '';

        return {
            tag: 'audio',
            controls: 'controls',
            poster: image,
            children: [
                {
                    tag: 'source',
                    src: audio,
                    type: 'audio/mpeg'
                }
            ]
        };
        return '<embed src="' + Phlexible.bundleAsset('/phlexiblemediamanager/flash/player.swf') + '" width="' + width + '" height="' + height + '" allowfullscreen="true" allowscriptaccess="always" wmode="transparent" flashvars="' + link + '" />';
    },

    createVideoPlayer: function (width, height, fileId, fileVersion, fileName, cache) {
        if (!cache._mm_preview_video || cache._mm_preview_video_mp4.substr(0, 2) !== 'ok' || cache._mm_preview_video_ogg.substr(0, 2) !== 'ok') {
            return this.createImage(256, 256, fileId, fileVersion, fileName, cache);
        }

        var image = this.getLink(fileId, '_mm_preview_player', fileVersion, cache);
        var video_mp4 = this.getLink(fileId, '_mm_preview_video_mp4', fileVersion, false) + '/name/' + fileName + '.mp4';
        var video_ogg = this.getLink(fileId, '_mm_preview_video_ogg', fileVersion, false) + '/name/' + fileName + '.ogv';
        //var link = '&file=' + video + '&image=' + image + '&height=' + height + '&width=' + width + '&overstretch=false';

        return {
            tag: 'video',
            controls: 'controls',
            poster: image,
            width: width,
            height: height,
            children: [
                {
                    tag: 'source',
                    src: video_mp4,
                    type: 'video/mp4'
                },
                {
                    tag: 'source',
                    src: video_ogg,
                    type: 'video/ogg'
                }
            ]
        };
        return '<embed src="' + Phlexible.bundleAsset('/phlexiblemediamanager/flash/player.swf') + '" width="' + width + '" height="' + height + '" allowfullscreen="true" allowscriptaccess="always" wmode="transparent" flashvars="' + link + '" />';
    },

    createImage: function (width, height, fileId, fileVersion, fileName, cache) {
        var link = this.getLink(fileId, '_mm_extra', fileVersion, cache);

        return {
            tag: 'img',
            style: 'border: 1px solid lightgray;',
            alt: fileName,
            src: link,
            width: width,
            height: height
        };
        return '<img style="border: 1px solid lightgray;" alt="' + fileName + '" src="' + link + '" width="' + width + '" height="' + height + '" />';
    },

    createText: function (width, height, fileId, fileName) {

    }
});
