Ext.define('Phlexible.mediamanager.window.FileReplaceWindow', {
    extend: 'Ext.window.Window',

    title: '_FileReplaceWindow',
    iconCls: Phlexible.Icon.get('document--exclamation'),
    width: 500,
    minWidth: 500,
    height: 400,
    minHeight: 400,
    bodyPadding: 5,
    cls: 'p-mediamanager-file-replace',
    modal: true,
    closable: false,

    sizeText: '_sizeText',
    fileConflictText: '_fileConflictText',
    fileConflictDescriptionText: '_fileConflictDescriptionText',
    applyToRemainingConflictsText: '_applyToRemainingConflictsText',
    deleteUploadedFileText: '_deleteUploadedFileText',
    deleteUploadedFileDescriptionText: '_deleteUploadedFileDescriptionText',
    replaceUploadedFileText: '_replaceUploadedFileText',
    replaceUploadedFileDescriptionText: '_replaceUploadedFileDescriptionText',
    keepBothFilesText: '_keepBothFilesText',
    keepBothFilesDescriptionText: '_keepBothFilesDescriptionText',

    initComponent: function () {
        this.initMyTemplates();
        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyTemplates: function() {
        this.fileReplaceTpl = new Ext.XTemplate(
            '<tpl for=".">',
            '<div class="p-mediamanager-file-replace-wrap">',
            '<div style="float: left;">',
            '<img src="/bundles/phlexiblegui/images/blank.gif" width="16" height="16" class="p-inline-icon {iconCls}">',
            '</div>',
            '<div style="padding-left: 20px;">',
            '<div class="p-mediamanager-file-replace-header">',
            '{header}',
            '</div>',
            '<div class="p-mediamanager-file-replace-text">',
            '{text}',
            '</div>',
            '<tpl if="src">',
            '<div>',
            '<div class="p-mediamanager-file-replace-img">',
            '<img src="{src}" width="48" height="48">',
            '</div>',
            '<div class="p-mediamanager-file-replace-desc">',
            '<div class="p-mediamanager-file-replace-name">{[Ext.String.ellipsis(values.name, 50)]}</div>',
            '<tpl if="values.type">',
            //'<div class="p-mediamanager-file-replace-type">{[Phlexible.mediatype.MediaTypes.getText(values.type)]}</div>',
            '</tpl>',
            '<div class="p-mediamanager-file-replace-size">' + this.sizeText + ': {[Phlexible.Format.size(values.size)]}</div>',
            '</div>',
            '</div>',
            '</tpl>',
            '</div>',
            '</div>',
            '</tpl>'
        );
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'container',
                border: false,
                plain: true,
                cls: 'p-mediamanager-file-replace-win-header',
                html: this.fileConflictText
            },
            {
                xtype: 'container',
                border: false,
                plain: true,
                padding: "0 0 10 0",
                cls: 'p-mediamanager-file-replace-win-desc',
                html: this.fileConflictDescriptionText
            },
            {
                xtype: 'dataview',
                itemSelector: 'div.p-mediamanager-file-replace-wrap',
                cls: 'p-mediamanager-file-replace-view',
                overItemCls: 'p-mediamanager-file-replace-wrap-over',
                singleSelect: true,
                store: Ext.create('Ext.data.Store', {
                    fields: ['action', 'header', 'text', 'fileId', 'name', 'type', 'size', 'src']
                }),
                tpl: this.fileReplaceTpl,
                listeners: {
                    select: this.saveFile,
                    scope: this
                }
            }
        ];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            itemId: 'buttons',
            dock: 'bottom',
            ui: 'footer',
            hidden: true,
            items: [
                '->',
            {
                xtype: 'checkbox',
                itemId: 'all',
                boxLabel: Ext.String.format(this.applyToRemainingConflictsText, 0)
            }]
        }];
    },

    getDataView: function() {
        return this.getComponent(2);
    },

    loadFile: function () {
        var file = this.uploadChecker.getCurrent(),
            actions = [];

        actions.push({
            action: 'discard',
            iconCls: Phlexible.Icon.get('document-shred'),
            header: this.deleteUploadedFileText,
            text: this.deleteUploadedFileDescriptionText,
            fileId: file.oldId,
            name: file.oldName,
            type: file.oldType,
            size: file.oldSize,
            src: Phlexible.Router.generate('mediamanager_media', {fileId: file.oldId, templateKey: '_mm_medium'})
        });

        if (!file.versions) {
            actions.push({
                action: 'replace',
                iconCls: Phlexible.Icon.get('document-break'),
                header: this.replaceUploadedFileText,
                text: this.replaceUploadedFileDescriptionText,
                fileId: file.newId,
                name: file.oldName,
                type: file.newType,
                size: file.newSize,
                src: Phlexible.Router.generate('mediamanager_upload_preview', {key: file.tempKey, id: file.tempId, template: '_mm_medium'})
            });
        } else {
            actions.push({
                action: 'add_version',
                iconCls: Phlexible.Icon.get('document--plus'),
                header: 'Als neue Version der bestehenden Datei speichern.',
                text: 'Es werden keine Daten verändert. Die vorhandene Datei wird um diese Datei ergänzt:',
                fileId: file.newId,
                name: file.newName,
                type: file.newType,
                size: file.newSize,
                src: Phlexible.Router.generate('mediamanager_upload_preview', {key: file.tempKey, id: file.tempId, template: '_mm_medium'})
            });
        }

        actions.push({
            action: 'keep',
            iconCls: Phlexible.Icon.get('plus-circle'),
            header: this.keepBothFilesText,
            text: Ext.String.format(this.keepBothFilesDescriptionText, Ext.String.ellipsis(file.alternativeName, 60)),
            fileId: '',
            name: '',
            type: '',
            size: '',
            src: ''
        });

        this.getDataView().getStore().loadData(actions);

        if (this.uploadChecker.count() > 1) {
            this.getDockedComponent('buttons').getComponent('all').setBoxLabel(Ext.String.format(this.applyToRemainingConflictsText, this.uploadChecker.count()));
            this.getDockedComponent('buttons').show();
        }
    },

    saveFile: function (view, record) {
        var all = this.getDockedComponent('buttons').getComponent('all').getValue() ? true : false,
            action = record.get('action');

        this.fireEvent('save', action, all);
    }
});
