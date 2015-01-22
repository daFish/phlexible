Ext.define('Phlexible.mediamanager.toolbar.UploadStatusBar', {
    extend: 'Ext.toolbar.Toolbar',
    alias: 'widget.mediamanager-upload-statusbar',

    height: 26,

    queued: 0,
    current: 0,
    files: null,
    startFn: Ext.emptyFn,
    stopFn: Ext.emptyFn,

    initComponent: function () {
        this.files = new Ext.util.MixedCollection();

        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'progressbar',
                itemId: 'progress',
                width: 100,
                value: 0,
                text: '',
                hidden: true
            },
            {
                itemId: 'pauseBtn',
                iconCls: Phlexible.Icon.get('control-pause'),
                handler: Ext.emptyFn,
                scope: this,
                hidden: true
            },
            {
                itemId: 'clearBtn',
                iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                handler: this.clear,
                scope: this,
                hidden: true
            },
            {
                xtype: 'tbseparator',
                itemId: 'separator'
            }
        ];
    },

    initMyListeners: function() {
        this.on({
            render: function (c) {
                c.listLayer = new Ext.Layer({
                    shadow: false,
                    cls: 't-mediamanager-upload-files-layer',
                    constrain: false
                });
                c.listLayer.setWidth(300);
                c.listLayer.setHeight(100);
                c.listLayerInner = c.listLayer.createChild({

                });
                c.listPanel = new Ext.Panel({
                    //title: 'test',
                    applyTo: c.listLayerInner,
                    frame: true
                });
                c.listPanel.setWidth(300);
                c.listPanel.setHeight(100);

                c.listTpl = new Ext.XTemplate('<tpl for="."><div>{fileName}</div></tpl>');
            },
            scope: this
        });
    },

    addFile: function (id, name, size, removeFn) {
        Phlexible.console.log('UploadStatusBar::addFile(' + id + ')');
        var iconCls = Phlexible.Icon.get('document');
        var ext = name.split('.').pop();
        if (Phlexible.documenttypes.DocumentTypes.classMap[ext]) {
            iconCls = Phlexible.documenttypes.DocumentTypes.classMap[ext].cls + '-small';
        }
        var text = name;
        if (size) {
            text += ' (' + Phlexible.Format.size(size) + ')';
        }
        var btn = this.add({
            //xtype: 'tbtext',
            iconCls: iconCls,
            text: text,
            handler: function () {
                this.removeFile(id);
            },
            scope: this,
            fileId: id,
            removeFn: removeFn
        });
        this.files.add(id, btn);
        this.getComponent('pauseBtn').show();
    },

    clear: function () {
        Phlexible.console.log('UploadStatusBar::clear()');
        this.files.each(function (file) {
            this.removeFile(file.fileId);
        }, this);
    },

    removeButton: function (btn) {
        btn.removeFn();
        btn.destroy();
        this.files.remove(btn);

        if (!this.files.getCount()) {
            this.getComponent('clearBtn').hide();
            this.getComponent('pauseBtn').hide();
            this.getComponent('progress').hide();
            this.getComponent('separator').hide();
        }
    },

    removeFile: function (id) {
        Phlexible.console.log('UploadStatusBar::removeFile(' + id + ')');
        var btn = this.files.get(id);
        this.removeButton(btn);
    },

    setActive: function (id) {
        Phlexible.console.log('UploadStatusBar::setActive(' + id + ')');
        this.getComponent('progress').updateProgress(0, '');
        this.files.get(id).el.down('span.x-btn-inner').setStyle('font-weight', 'bold');
    },

    setProgress: function (id, percent) {
        Phlexible.console.debug('UploadStatusBar::setProgress(' + id + ', ' + percent + ')');
        this.getComponent('progress').updateProgress(parseInt(percent / 100, 10), percent + '%');
    },

    setFinished: function (id) {
        Phlexible.console.log('UploadStatusBar::setFinished(' + id + ')');
        this.getComponent('progress').updateProgress(1, '');
        this.removeFile(id);
    },

    setError: function (code, msg, id, name) {
        Phlexible.console.log('UploadStatusBar::setError(' + id + ', ' + code + ', ' + msg + ', ' + name + ')');
        this.getComponent('progress').updateProgress(1, '');
        this.removeFile(id);
    },

    start: function () {
        Phlexible.console.log('UploadStatusBar::start()');
        this.getComponent('clearBtn').hide();
        this.getComponent('pauseBtn').setIconCls(Phlexible.Icon.get('control-pause'));
        this.getComponent('pauseBtn').handler = this.stopFn;
        this.getComponent('progress').show();
    },

    stop: function () {
        Phlexible.console.log('UploadStatusBar::stop()');
        //this.getComponent('progress').hide();
        this.getComponent('progress').updateProgress(0, '');
        this.getComponent('pauseBtn').setIconCls(Phlexible.Icon.get('control'));
        this.getComponent('pauseBtn').handler = this.startFn;
        if (this.files.getCount()) {
            this.getComponent('clearBtn').show();
        } else {
            this.getComponent('clearBtn').hide();
        }
    },

    bindUploader: function(uploader) {
        this.startFn = function () {
            Phlexible.console.log('UploadStatusBar::startFn()');
            uploader.start();
        };

        this.stopFn = function () {
            Phlexible.console.log('UploadStatusBar::stopFn()');
            uploader.stop();
        };

        uploader.bind('FilesAdded', function (up, files) {
            Ext.each(files, function (file) {
                this.addFile(file.id, file.name, file.size, function () {
                    up.removeFile(file);
                });
                Phlexible.console.debug('uploader::FilesAdded', 'id:' + file.id, 'name:' + file.name, 'size:' + plupload.formatSize(file.size));
            }, this);
        }, this);

        uploader.bind('StateChanged', function (up) {
            Phlexible.console.log('uploader::StateChanged', 'state:' + up.state);
            if (up.state == plupload.STARTED) {
                this.start();
            } else if (up.state == plupload.STOPPED) {
                this.stop();
            }
        }, this);

        uploader.bind('BeforeUpload', function (up, file) {
            this.setActive(file.id);
            Phlexible.console.log('uploader::BeforeUpload', 'id:' + file.id, file);
        }, this);

        uploader.bind('UploadProgress', function (up, file) {
            this.setProgress(file.id, file.percent);
            Phlexible.console.debug('uploader::UploadProgress', 'id:' + file.id, 'percent:' + file.percent);
        }, this);

        uploader.bind('Error', function (up, err) {
            this.setError(err.code, err.message, err.file ? err.file.id : "", err.file ? err.file.name : "");
            Phlexible.console.error('uploader::Error', 'code:' + err.code, 'message:' + err.message, 'file:' + (err.file ? err.file.name : ""));
        }, this);

        uploader.bind('FileUploaded', function (up, file, info) {
            this.setFinished(file.id);
            Phlexible.console.log('uploader::FileUploaded', 'id:' + file.id, 'info:', info);
        }, this);

    }
});