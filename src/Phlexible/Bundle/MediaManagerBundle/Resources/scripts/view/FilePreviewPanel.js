Ext.provide('Phlexible.mediamanager.FilePreviewPanel');

Phlexible.mediamanager.FilePreviewPanel = Ext.extend(Ext.Panel, {
    strings: Phlexible.mediamanager.Strings,
    title: Phlexible.mediamanager.Strings.preview,
    cls: 'p-mediamanager-preview-panel',
    height: 270,

    file_id: null,
    file_version: null,

    // private
    initComponent: function () {
        if (this.file_id && this.file_version && this.file_name && this.media_type && this.cache) {
            this.html = this.getHtml(this.file_id, this.file_version, this.file_name, this.media_type, this.cache);
        }
        else {
            this.html = this.createNoPreview();
        }

        Phlexible.mediamanager.FilePreviewPanel.superclass.initComponent.call(this);
    },

    loadRecord: function (r) {
        var file_id = r.get('id');
        var file_name = r.get('name');
        var file_version = r.get('version');
        var media_type = r.get('media_type');
        var cache = r.get('cache');

        this.load(file_id, file_version, file_name, media_type, cache);
    },

    load: function (file_id, file_version, file_name, media_type, cache) {
        if (this.file_id != file_id || this.file_version != file_version) {
            this.file_id = file_id;
            this.file_version = file_version;
            this.body.update('');
            this.body.insertFirst(this.getHtml(file_id, file_version, file_name, media_type, cache));
        }
    },

    getHtml: function (file_id, file_version, file_name, media_type, cache) {
        switch (media_type.substr(0, 5)) {
            case 'audio':
                return this.createAudioPlayer(256, 256, file_id, file_version, file_name, cache);
                break;

            case 'video':
                return this.createVideoPlayer(256, 256, file_id, file_version, file_name, cache);
                break;

            case 'image':
            default:
                return this.createImage(256, 256, file_id, file_version, file_name, cache);
                break;
        }
    },

    getLink: function (file_id, template_key, file_version, cache) {
        if (cache && template_key && cache[template_key]) {
            return cache[template_key];
        }

        var parameters = {
            file_id: file_id,
            template_key: template_key
        };
        if (file_version) {
            parameters['file_version'] = file_version;
        }
        if (cache && cache[template_key]) {
            parameters['cache'] = cache[template_key];
        } else if (cache !== false) {
            parameters['waiting'] = 1;
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
                            html: this.strings.no_preview_available
                        }
                    ]
                }
            ]
        };
        //return '<table border="0" width="100%" height="100%"><tr><td align="center" valign="middle">' + this.strings.no_preview_available + '</td></tr></table>';
    },

    createAudioPlayer: function (width, height, file_id, file_version, file_name, cache) {
        if (!cache._mm_preview_audio) {
            return this.createImage(256, 256, file_id, file_version, file_name, cache);
        }

        //var image = this.getLink(file_id, '_mm_preview_player', file_version, cache);
        //var audio = cache._mm_preview_audio;//this.getLink(file_id, '_mm_preview_audio', file_version, false) + '/name/' + file_name + '.mp3';
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

    createVideoPlayer: function (width, height, file_id, file_version, file_name, cache) {
        if (!cache._mm_preview_video_mp4 || !cache._mm_preview_video_ogg) {
            return this.createImage(256, 256, file_id, file_version, file_name, cache);
        }

        //var image = cache._mm_preview_player || null; //this.getLink(file_id, '_mm_preview_player', file_version, cache);
        //var video_mp4 = cache._mm_preview_video_mp4 || null; //this.getLink(file_id, '_mm_preview_video_mp4', file_version, false) + '/name/' + file_name + '.mp4';
        //var video_ogg = cache._mm_preview_video_ogg || null; //this.getLink(file_id, '_mm_preview_video_ogg', file_version, false) + '/name/' + file_name + '.ogv';
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

    createImage: function (width, height, file_id, file_version, file_name, cache) {
        //if (!cache._mm_extra) {
        //    return this.createNoPreview();
        //}

        //var link = this.getLink(file_id, '_mm_extra', file_version, cache);

        return {
            tag: 'img',
            style: 'border: 1px solid lightgray;',
            alt: file_name,
            src: this.getLink(file_id, '_mm_extra', file_version, cache), //cache._mm_extra, //link,
            width: width,
            height: height
        };
        //return '<img style="border: 1px solid lightgray;" alt="' + file_name + '" src="' + link + '" width="' + width + '" height="' + height + '" />';
    }
});

Ext.reg('mediamanager-filepreviewpanel', Phlexible.mediamanager.FilePreviewPanel);
