Ext.define('Phlexible.mediamanager.view.FilePreview', {
    extend: 'Ext.panel.Panel',
    xtype: 'mediamanager.file-preview',

    cls: 'p-mediamanager-preview',

    fileId: null,
    fileVersion: null,

    noPreviewAvailableText: '_noPreviewAvailableText',

    // private
    initComponent: function () {
        if (this.file) {
            this.html = this.getHtml(this.file);
        }
        else {
            this.html = this.createNoPreview();
        }

        this.callParent(arguments);
    },

    loadRecord: function (file) {
        this.load(file);
    },

    load: function (file) {
        if (this.fileId != file.getId() || this.fileVersion != file.get('version')) {
            this.fileId = file.getId();
            this.fileVersion = file.get('version');

            //this.body.update('');
            this.update(this.getHtml(file));
        }
    },

    getHtml: function (file) {
        var parts = file.get('mediaType').split(':');
        switch (parts[0]) {
            case Phlexible.mediamanager.AUDIO:
                return this.createAudioPlayer(256, 256, file);
                break;

            case Phlexible.mediamanager.VIDEO:
                return this.createVideoPlayer(256, 256, file);
                break;

            case Phlexible.mediamanager.IMAGE:
            default:
                return this.createImage(256, 256, file);
        }
    },

    getLink: function (file, templateKey) {
        if (file.get('cache') && templateKey && file.get('cache')[templateKey]) {
            return file.get('cache')[templateKey];
        }

        var parameters = {
            fileId: file.getId(),
            templateKey: templateKey
        };
        if (file.get('version')) {
            parameters.fileVersion = file.get('version');
        }

        return Phlexible.Router.generate('phlexible_mediamanager_media', parameters);
    },

    empty: function () {
        this.update(this.createNoPreview());
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

    createAudioPlayer: function (width, height, file) {
        var cache = file.get('cache');
        if (!cache._mm_preview_audio || cache._mm_preview_audio.substr(0, 2) !== 'ok') {
            return this.createImage(256, 256, file);
        }

        var image = this.getLink(file, '_mm_preview_player');
        var audio = this.getLink(file, '_mm_preview_audio') + '/name/' + fileName + '.mp3';
        //var link = '&file=' + audio + '&image=' + image + '&height=' + height + '&width=' + width + '';

        var config = {
            tag: 'audio',
            controls: 'controls',
            style: {
                width: '256px',
                height: '256px'
            },
            children: []
        };

        if (cache._mm_preview_player) {
            config.poster = cache._mm_preview_player;
            config.style["background-image"] = "url('" + cache._mm_preview_player + "')";
        }

        if (cache._mm_preview_audio) {
            config.children.push({
                tag: 'source',
                src: cache._mm_preview_audio,
                type: 'audio/mpeg'
            });
        }

        return config;
        //return '<embed src="' + Phlexible.bundleAsset('/phlexiblemediamanager/flash/player.swf') + '" width="' + width + '" height="' + height + '" allowfullscreen="true" allowscriptaccess="always" wmode="transparent" flashvars="' + link + '" />';
    },

    createVideoPlayer: function (width, height, file) {
        var cache = file.get('cache');
        if (!cache._mm_preview_video || cache._mm_preview_video_mp4.substr(0, 2) !== 'ok' || cache._mm_preview_video_ogg.substr(0, 2) !== 'ok') {
            return this.createImage(256, 256, fileId, fileVersion, fileName, cache);
        }

        var image = this.getLink(fileId, '_mm_preview_player', fileVersion, cache);
        var video_mp4 = this.getLink(fileId, '_mm_preview_video_mp4', fileVersion, false) + '/name/' + fileName + '.mp4';
        var video_ogg = this.getLink(fileId, '_mm_preview_video_ogg', fileVersion, false) + '/name/' + fileName + '.ogv';
        //var link = '&file=' + video + '&image=' + image + '&height=' + height + '&width=' + width + '&overstretch=false';

        var config = {
            tag: 'video',
            controls: 'controls',
            //width: width,
            //height: height,
            style: {
                width: '256px',
                height: '256px'
            },
            children: []
        };

        if (cache._mm_preview_player) {
            config.poster = cache._mm_preview_player;
        }
        if (cache._mm_preview_video_mp4) {
            config.children.push({
                tag: 'source',
                src: cache._mm_preview_video_mp4,
                type: 'video/mp4'
            })
        }

        if (cache._mm_preview_video_ogg) {
            config.children.push({
                tag: 'source',
                src: cache._mm_preview_video_ogg,
                type: 'video/mp4'
            })
        }

        return config;
        //return '<embed src="' + Phlexible.bundleAsset('/phlexiblemediamanager/flash/player.swf') + '" width="' + width + '" height="' + height + '" allowfullscreen="true" allowscriptaccess="always" wmode="transparent" flashvars="' + link + '" />';
    },

    createImage: function (width, height, file) {
        var link = this.getLink(file, '_mm_extra');

        return {
            tag: 'img',
            style: 'border: 1px solid lightgray;',
            alt: file.get('name'),
            src: link,
            width: width,
            height: height
        };
        return '<img style="border: 1px solid lightgray;" alt="' + file.get('name') + '" src="' + link + '" width="' + width + '" height="' + height + '" />';
    },

    createText: function (width, height, fileId, fileName) {

    }
});
