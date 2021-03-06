Ext.define('Phlexible.cms.util.Helper', {
    diffFn: function (diff, panel) {
        var contentFrom, contentTo, split, id, version, html;

        if (diff.contentFrom) {
            split = diff.contentFrom.split(';');
            id = split[0];
            version = split[1] || 1;

            contentFrom = '<img src="' + Phlexible.Router.generate('mediamanager_media', {file_id: id, template_key: '_mm_large', file_version: version}) + '" width="96" height="96" style="border: 1px solid #99BBE8;" />';
        }
        else {
            contentFrom = '(empty)';
        }
        split = diff.contentTo.split(';');
        id = split[0];
        version = split[1] || 1;
        contentTo = '<img src="' + Phlexible.Router.generate('mediamanager_media', {file_id: id, template_key: '_mm_large', file_version: version}) + '" width="96" height="96" style="border: 1px solid #99BBE8;" />';

        html = '<label>' + Phlexible.elements.Strings.version + ' ' + panel.diff.versionFrom + '</label>' +
            '<div>' + contentFrom + '</div>' +
            '<label>' + Phlexible.elements.Strings.version + ' ' + panel.diff.versionTo + '</label>' +
            '<div>' + contentTo + '</div>';

        panel.body.update(html);
    },

    inlineDiff: function (targetEl, clickEl) {
        if (!this.element || !this.diff || (this.diff.type && !this.diff.contentFrom)) {
            return;
        }

        if (!targetEl) targetEl = this.el;
        if (!clickEl) clickEl = targetEl;

        targetEl.on('click', function () {
            if (this.element.activeDiffEl && this.element.activeDiffEl.isVisible() && !e.within(targetEl.dom, false, true) && !e.within(this.element.activeDiffEl.dom, false, true)) {
                this.element.activeDiffEl.hide();
                this.element.activeDiffEl = null;
            }

            if (!this.diffEl) {
                var height = (targetEl.getHeight && targetEl.getHeight() > 32) ? targetEl.getHeight() : 32;
                var html = '';
                if (this.diff.type == 'change') {
                    var split = this.diff.contentFrom.split(';');
                    var id = split[0];
                    var version = split[1] || 1;

                    html = '<img src="' + Phlexible.Router.generate('mediamanager_media', {file_id: id, template_key: '_mm_large', file_version: version}) + '" width="96" height="96" style="border: 1px solid #99BBE8;" />';
                }
                else {
                    html = Phlexible.fields.Strings.diff_new_field;
                    height = 14;
                }

                this.diffEl = targetEl.insertSibling({
                    tag: 'div',
                    html: html,
                    cls: 'p-fields-diff-inline',
                    style: 'height: ' + height + 'px;'
                }, 'after');
                this.diffEl.setVisibilityMode(Ext.Element.DISPLAY);
                this.element.activeDiffEl = this.diffEl;
            }
            else {
                if (!this.diffEl.isVisible()) {
                    this.diffEl.show();
                    this.element.activeDiffEl = this.diffEl;
                }
            }
        }, this);
    },

    unlink: function () {
        if (this.isSynchronized) {
            if (this.isMaster) {
                this.el.addClass('p-fields-synchronized-master');
            }
            else {
                if (this.isUnlinked) {
                    this.el.addClass('p-fields-synchronized-unlinked');
                }
                else {
                    this.el.addClass('p-fields-synchronized-synched');
                    this.disable();
                }

                if (this.hasUnlink && !this.isDiff) {
                    this.hidden_unlink = this.el.parent().last().insertSibling({
                        tag: 'input',
                        type: 'hidden',
                        name: 'unlink_' + this.name,
                        value: this.isUnlinked ? '1' : ''
                    }, 'after');
                    this.button_unlink = this.hidden_unlink.insertSibling({
                        tag: 'img',
                        src: Phlexible.bundleAsset('/elementtypes/icons/' + (this.isUnlinked ? 'unlink' : 'link')) + '.png',
                        width: 16,
                        height: 16,
                        style: 'cursor: pointer; padding-left: 3px; vertical-align: middle;',
                        qtip: this.isUnlinked ? 'Link' : 'Unlink'
                    }, 'after');
                    this.button_unlink.on('click', function () {
                        if (this.isUnlinked) {
                            this.isUnlinked = false;
                            this.hidden_unlink.set({
                                value: ''
                            });
                            this.button_unlink.set({
                                src: Phlexible.bundleAsset('/elementtypes/icons/link.png'),
                                qtip: 'Unlink'
                            });
                            this.el.removeClass('p-fields-synchronized-unlinked');
                            this.el.addClass('p-fields-synchronized-synched');
                            this.disable();
                            if (this.masterValue) {
                                this.setFile(this.masterValue.fileId, this.masterValue.fileVersion, this.masterValue.name, this.masterValue.folder_id);
                            }
                            else {
                                this.reset();
                            }
                        }
                        else {
                            this.isUnlinked = true;
                            this.hidden_unlink.set({
                                value: '1'
                            });
                            this.button_unlink.set({
                                src: Phlexible.bundleAsset('/elementtypes/icons/unlink.png'),
                                qtip: 'Link'
                            });
                            this.el.addClass('p-fields-synchronized-unlinked');
                            this.el.removeClass('p-fields-synchronized-synched');
                            this.enable();
                        }
                    }, this);
                }
            }
        }
    }
});
