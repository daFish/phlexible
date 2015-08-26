Ext.define('Phlexible.mediamanager.util.UploadChecker', {
    extend: 'Ext.util.Observable',

    /**
     * @event reload
     */

    /**
     * @param {Object} config
     */
    constructor: function(config) {
        this.running = false;

        this.callParent(arguments);
    },

    check: function() {
        if (this.isRunning()) {
            return;
        }

        this.next();
    },

    /**
     * @private
     */
    next: function() {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_upload_check'),
            success: this.onCheckResponse,
            scope: this
        });
    },

    isRunning: function() {
        return this.running;
    },

    count: function() {
        return this.current.total;
    },

    getCurrent: function() {
        return this.current;
    },

    onCheckResponse: function(response) {
        if (!response.responseText) {
            this.running = false;
            if (this.replace) {
                this.replace.hide();
            }
            if (this.wizard) {
                this.wizard.hide();
            }
            return;
        }

        var data = Ext.decode(response.responseText);

        if (!data || !data.tempId) {
            this.running = false;
            if (this.replace) {
                this.replace.hide();
            }
            if (this.wizard) {
                this.wizard.hide();
            }
            return;
        }

        this.current = data;

        if (data.wizard) {
            if (this.replace) {
                this.replace.hide();
            }
            if (!this.wizard) {
                this.wizard = Ext.xcreate('Phlexible.mediamanager.window.FileUploadWizard', {
                    uploadChecker: this,
                    listeners: {
                        update: function () {
                            this.getFilesGrid().getStore().reload();
                        },
                        scope: this
                    }
                });
            }
            this.wizard.show();
            this.wizard.loadFile();
        }
        else {
            if (this.wizard) {
                this.wizard.hide();
            }
            if (!this.replace) {
                this.replace = Ext.create('Phlexible.mediamanager.window.FileReplaceWindow', {
                    uploadChecker: this,
                    listeners: {
                        save: function(action, all) {
                            var file = this.getCurrent(),
                                params = {
                                    all: all ? 1 : 0,
                                    tempKey: file.tempKey,
                                    tempId: file.tempId,
                                    action: action
                                };

                            var request = {
                                url: Phlexible.Router.generate('mediamanager_upload_save'),
                                params: params,
                                success: function (response) {
                                    var data = Ext.decode(response.responseText);

                                    if (data.data.action !== 'discard') {
                                        this.fireEvent('reload');
                                    }
                                    this.next();
                                },
                                failure: function (response) {
                                    var result = Ext.decode(response.responseText);

                                    Ext.MessageBox.alert('Failure', result.msg);
                                },
                                scope: this
                            };

                            Ext.Ajax.request(request);
                        },
                        scope: this
                    }
                });
            }
            this.replace.show();
            this.replace.loadFile();
        }

        this.running = true;
    }
});
