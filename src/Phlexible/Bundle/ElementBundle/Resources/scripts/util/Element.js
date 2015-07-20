Ext.provide('Phlexible.element.Element');
Ext.provide('Phlexible.element.Teaser');

Ext.require('Phlexible.fields.Prototypes');
Ext.require('Phlexible.tree.window.NewElementInstanceWindow');
Ext.require('Phlexible.tree.window.NewElementWindow');

Phlexible.element.Element = function (config) {
    this.addEvents(
        'beforeLoad',
        'load',
        'createElement',
        'beforeSave',
        'save',
        'saveFailure',
        'enableSave',
        'disableSave',
        'internalSave',
        'beforePublish',
        'publish',
        'publishFailure',
        'beforPublishAdvanced',
        'publishAdvanced',
        'publishAdvancedFailure',
        'beforeSetOffline',
        'setOffline',
        'setOfflineFailure',
        'beforeSetOfflineAdvanced',
        'setOfflineAdvanced',
        'setOfflineAdvancedFailure',
        'beforeLock',
        'beforeUnlock',
        'getlock',
        'islocked',
        'removelock'
    );

    if (config.language) {
        this.language = config.language;
    }
    if (config.siterootId) {
        this.siterootId = config.siterootId;

        var checkedLanguage = this.language || Phlexible.Config.get('language.frontend');
        var siterootLanguages = Phlexible.Config.get('user.siteroot.languages')[this.siterootId];
        var langBtns = [], hasChecked = false;
        for (var i = 0; i < Phlexible.Config.get('set.language.frontend').length; i++) {
            var languageRow = Phlexible.Config.get('set.language.frontend')[i];
            if (siterootLanguages.indexOf(languageRow[0]) === -1) {
                continue;
            }
            if (languageRow[0] === Phlexible.Config.get('language.frontend')) {
                hasChecked = true;
            }
            langBtns.push({
                text: languageRow[1],
                iconCls: languageRow[2],
                langKey: languageRow[0],
                checked: languageRow[0] === checkedLanguage
            });
        }
        if (!hasChecked) {
            langBtns[0].checked = true;
            this.language = langBtns[0].langKey;
        }

        this.languages = langBtns;
    }
    if (config.startParams) {
        this.startParams = config.startParams;
    }

    Ext.getDoc().on('mousedown', function (e) {
        if (this.activeDiffEl && this.activeDiffEl.isVisible()) {
            this.activeDiffEl.hide();
            this.activeDiffEl = null;
        }
    }, this);

    this.prototypes = new Phlexible.fields.Prototypes();

    this.history = new Ext.util.MixedCollection();
};

Ext.extend(Phlexible.element.Element, Ext.util.Observable, {
    nodeId: null,
    teaserId: null,
    type: null,
    eid: null,
    language: null,
    version: null,
    createdAt: null,
    createdBy: null,
    latestVersion: null,
    masterLanguage: null,
    isMaster: null,
    comment: null,
    defaultTab: null,
    defaultContentTab: null,
    valueStructure: null,
    structure: null,
    elementtypeId: null,
    elementtypeName: null,
    elementtypeRevision: null,
    elementtypeType: null,
    meta: null,
    diff: null,
    pager: null,
    urls: null,
    permissions: null,
    instances: null,
    configuration: null,
    allowedChildren: null,
    versions: null,
    lockInfo: null,

    treeNode: null,
    loaded: false,

    loadTreeNode: function(treeNode, version, language, doLock) {
        var loadParams = {
            id: treeNode.attributes.id,
            type: 'node',
            siterootId: null,
            language: language || this.language || null,
            version: version || null,
            lock: doLock ? 1 : 0
        };

        this.reload(loadParams);
        this.setTreeNode(treeNode);
    },

    loadEid: function (eid, version, language, doLock) {
        var loadParams = {
            id: null,
            eid: eid || null,
            siterootId: this.siterootId || null,
            language: language || this.language || null,
            version: version || null,
            lock: doLock ? 1 : 0
        };

        this.reload(loadParams);
    },

    loadTeaser: function (teaser_id, version, language, doLock) {
        var loadParams = {
            id: null,
            teaser_id: teaser_id,
            siterootId: this.siterootId || null,
            language: language || this.language || null,
            version: version || null,
            lock: doLock ? 1 : 0
        };

        this.reload(loadParams);
    },

    load: function (id, version, language, doLock) {
        var loadParams = {
            id: id || null,
            teaser_id: null,
            eid: null,
            siterootId: null,
            language: language || this.language || null,
            version: version || null,
            lock: doLock ? 1 : 0
        };

        //this.mask.msg = 'Loading Element ' + eid;
        //this.mask.show();

        this.reload(loadParams);

        //return;
    },

    reload: function (params) {
        if (!params) {
            params = {};
        }

        if (params.id === undefined) {
            params.id = this.tid;
        }

        if (params.siterootId === undefined) {
            params.siterootId = this.siterootId;
        }

        if (params.language === undefined) {
            params.language = this.language;
        }

        if (params.version !== null && params.version === undefined && this.version) {
            params.version = this.version;
        }

        if (((this.tid && this.tid != params.id) || (this.language && this.language != params.language)) &&
            this.lockinfo && this.lockinfo.status == 'edit') {
            params.unlock = this.lockinfo.id;
        }

        if (!params.lock && this.lockinfo) {
            params.lock = 1;
        }

        if (this.fireEvent('beforeLoad', params) === false) {
            return;
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('elements_data_load'),
            params: params,
            success: this.onLoadSuccess,
            failure: this.onLoadFailure,
            scope: this
        });
    },

    onLoadSuccess: function (response) {
        var elementData = Ext.decode(response.responseText);

        if (elementData.success) {
            this.nodeId = elementData.nodeId;
            this.teaserId = elementData.teaserId;
            this.type = elementData.type;
            this.eid = elementData.eid;
            this.language = elementData.language;
            this.version = elementData.version;
            this.createdAt = elementData.createdAt;
            this.createdBy = elementData.createdBy;
            this.latestVersion = elementData.latestVersion;
            this.masterLanguage = elementData.masterLanguage;
            this.isMaster = elementData.isMaster;
            this.comment = elementData.comment;
            this.defaultTab = elementData.defaultTab;
            this.defaultContentTab = elementData.defaultContentTab;
            this.valueStructure = elementData.valueStructure;
            this.structure = elementData.structure;
            this.elementtypeId = elementData.elementtypeId;
            this.elementtypeRevision = elementData.elementtypeRevision;
            this.elementtypeName = elementData.elementtypeName;
            this.elementtypeType = elementData.elementtypeType;
            this.meta = elementData.meta;
            this.diff = elementData.diff;
            this.pager = elementData.pager;
            this.urls = elementData.urls;
            this.permissions = elementData.permissions;
            this.instances = elementData.instances;
            this.configuration = elementData.configuration;
            this.allowedChildren = elementData.allowedChildren;
            this.versions = elementData.versions;
            this.lockinfo = elementData.lockInfo;

            this.setLockStatus(this.lockinfo);

            this.loaded = true;

            this.fireEvent('load', this);

            this.fireEvent('afterload', this);
        } else {
            Ext.MessageBox.alert('Error', elementData.msg);

            this.setStatusLocked();
        }

        var historyKey = this.id + '_' + this.version + '_' + this.language;
        if (this.history.indexOfKey(historyKey) !== false) {
            this.history.removeKey(historyKey);
        }
        this.history.add(historyKey, [this.id, this.version, this.language, this.title, elementData.icon, new Date().getTime()]);
        this.fireEvent('historychange', this, this.history);
    },

    onLoadFailure: function () {
        Ext.MessageBox.alert('Error connecting to server.');
        this.setStatusLocked();
    },

    save: function(parameters) {
        parameters = parameters || {};

        if (!this.getNodeId()) {
            Ext.MessageBox.alert('Failure', 'Save not possible, no element loaded.');
            return;
        }

        parameters.tid = this.getNodeId();
        parameters.teaser_id = this.getTeaserId();
        parameters.eid = this.getEid();
        parameters.language = this.getLanguage();
        parameters.version = this.getVersion();

        var errors = [];

        this.fireEvent('internalSave', parameters, errors);

        if (errors.length) {
            Phlexible.console.error(errors);
            Ext.MessageBox.alert('Failure', 'Save not possible. ' + errors.join(' '));
            return;
        }

        //Phlexible.console.debug(parameters);

        Ext.Ajax.request({
            url: Phlexible.Router.generate('elements_data_save'),
            params: parameters,
            success: this.onSaveSuccess,
            failure: function (response) {
                var result = Ext.decode(response.responseText);

                if (!result) {
                    result = {
                        success: false,
                        msg: 'Error occured'
                    };
                }

                Ext.MessageBox.alert('Failure', result.msg);
                this.fireEvent('saveFailure', this, result);
            },
            scope: this
        });
    },

    onSaveSuccess: function(response) {
        var result = Ext.decode(response.responseText);

        if (!result) {
            result = {
                success: false,
                msg: 'An unexpected error occured',
                data: {}
            };
        }

        if (result.success) {
            Phlexible.success(result.msg);

            //this.fireEvent('save', this);
            this.fireEvent('save', this, result);

            if (result.data.publish) {
                this.fireEvent('publish', this, result);
            }

            this.reload({
                version: null
            });

            if (result.data.publish_other && result.data.publish_other.length) {
                var w = new Phlexible.element.PublishSlaveWindow({
                    data: result.data.publish_other
                });
                w.show();
            }
        } else {
            Ext.MessageBox.alert('Failure', data.msg);
            this.fireEvent('saveFailure', this, result);
        }
    },

    getLanguages: function() {
        return this.languages;
    },

    getSiterootId: function() {
        return this.siterootId;
    },

    getNodeId: function() {
        return this.nodeId;
    },

    getTeaserId: function() {
        return this.nodeId;
    },

    getType: function() {
        return this.type;
    },

    getEid: function() {
        return this.eid;
    },

    getLanguage: function () {
        return this.language;
    },

    getVersion: function() {
        return this.version;
    },

    getCreatedAt: function() {
        return this.createdAt;
    },

    getCreatedBy: function() {
        return this.createdBy;
    },

    getLatestVersion: function() {
        return this.latestVersion;
    },

    getMasterLanguage: function() {
        return this.masterLanguage;
    },

    getIsMaster: function() {
        return this.isMaster;
    },

    getComment: function() {
        return this.comment;
    },

    getDefaultTab: function() {
        return this.defaultTab;
    },

    getDefaultContentTab: function() {
        return this.defaultContentTab;
    },

    getValueStructure: function() {
        return this.valueStructure;
    },

    getStructure: function() {
        return this.structure;
    },

    getElementtypeId: function() {
        return this.elementtypeId;
    },

    getElementtypeName: function() {
        return this.elementtypeName;
    },

    getElementtypeRevision: function() {
        return this.elementtypeRevision;
    },

    getElementtypeType: function() {
        return this.elementtypeType;
    },

    getMeta: function() {
        return this.meta || [];
    },

    getDiff: function() {
        return this.diff || null;
    },

    getPager: function() {
        return this.pager || null;
    },

    getUrls: function() {
        return this.urls || {};
    },

    getPermissions: function() {
        return this.permissions || [];
    },

    getInstances: function() {
        return this.instances || [];
    },

    getConfiguration: function() {
        return this.configuration || {};
    },

    getAllowedChildren: function() {
        return this.allowedChildren || [];
    },

    getVersions: function() {
        return this.versions || [];
    },

    getLockInfo: function() {
        return this.lockInfo || [];
    },

    setLanguage: function (language, noReload) {
        if (language !== this.language) {
            this.fireEvent('setLanguage', this, language);
            if (!noReload && this.loaded) {
                this.reload({
                    language: language
                });
            }
            this.language = language;
        }
    },

    isGranted: function (permission) {
        return this.getPermissions().indexOf(permission) !== -1;
    },

    /**
     * @deprecated
     */
    isAllowed: function (permission) {
        return this.isGranted(permission);
    },

    setTreeNode: function (node) {
        this.treeNode = node;
        this.teaserNode = null;
    },

    getTreeNode: function () {
        return this.treeNode;
    },

    setTeaserNode: function (node) {
        this.teaserNode = node;
    },

    getTeaserNode: function () {
        return this.teaserNode;
    },

    showNewElementWindow: function (node, element_type_id) {
        if (!node) {
            node = this.getTreeNode();
        }

        if (node) {
            sort_mode = node.attributes.sort_mode;
        } else {
            sort_mode = null;
        }

        var w = new Phlexible.element.NewElementWindow({
            element_type_id: element_type_id,
            sort_mode: sort_mode,
            language: this.language,
            submitParams: {
                siterootId: this.siterootId,
                eid: node ? node.attributes.eid : this.eid,
                id: node ? node.id : this.tid
            },
            listeners: {
                success: function (dialog, result, node) {
                    this.fireEvent('createElement', this, result.data, node);
                    this.load(result.data.tid, null, result.data.master_language, true);
                }.createDelegate(this, [node], true)
            }
        });
        w.show();
    },

    showNewAliasWindow: function (node, element_type_id) {
        if (!node) {
            node = this.getTreeNode();
        }
        var w = new Phlexible.element.NewElementInstanceWindow({
            element_type_id: element_type_id,
            sort_mode: node.attributes.sort_mode,
            language: this.language,
            submitParams: {
                siterootId: this.siterootId,
                eid: node.attributes.eid,
                id: node.id
            },
            listeners: {
                success: function (node) {
                    node.attributes.children = false;
                    node.reload();
                }.createDelegate(this, [node], false)
            }
        });
        w.show();
    },

    diff: function (diff, cb, scope) {
        this.fireEvent('diff', diff, cb, scope);
    },

    clearDiff: function () {
        this.fireEvent('clearDiff');
    },

    getLockStatus: function () {
        if (this.lockinfo && (this.lockinfo.status == 'edit' || this.lockinfo.status == 'locked' || this.lockinfo.status == 'locked_permanently')) {
            return this.lockinfo.status;
        }

        return 'idle';
    },

    // protected
    setLockStatus: function (lockinfo) {
        if (!lockinfo) {
            this.setStatusIdle();
            return;
        }

        this.lockinfo = lockinfo;

        switch (lockinfo.status) {
            case 'edit':
                this.setStatusEdit();
                break;

            case 'locked':
                this.setStatusLocked();
                break;

            case 'locked_permanently':
                this.setStatusLockedPermanently();
                break;

            default:
                this.setStatusIdle();
        }
    },

    // protected
    setStatusLocked: function () {
        if (!this.lockinfo) {
            this.lockinfo = {};
        }

        this.lockinfo.status = 'locked';

        this.fireEvent('islocked', this);
    },

    // protected
    setStatusLockedPermanently: function () {
        if (!this.lockinfo) {
            this.lockinfo = {};
        }

        this.lockinfo.status = 'locked_permanently';

        this.fireEvent('islocked', this);
    },

    // protected
    setStatusEdit: function () {
        if (!this.lockinfo) {
            this.lockinfo = {};
        }

        this.lockinfo.status = 'edit';

        this.fireEvent('getlock', this);
    },

    // protected
    setStatusIdle: function () {
        this.lockinfo = null;

        this.fireEvent('removelock', this);
    },

    lock: function (callback) {
        if (this.lockinfo) {
            return false;
        }

        if (this.fireEvent('beforeLock', this) === false) {
            return false;
        }

        if (!callback) callback = this.onLockSuccess;

        Ext.Ajax.request({
            url: Phlexible.Router.generate('elements_locks_lock'),
            params: {
                eid: this.eid,
                language: this.language
            },
            success: callback,
            scope: this
        });

        return true;
    },

    // private
    onLockSuccess: function (response) {
        var data = Ext.decode(response.responseText);

        if (data.success) {
            //this.setStatusEdit();
            this.reload();
            Phlexible.success('Lock acquired.');
        }
        else {
            this.setStatusLocked();
            Ext.MessageBox.alert('Failure', data.msg);
        }
    },

    unlock: function (callback) {
        if (!this.lockinfo || this.lockinfo.status != 'edit') {
            return false;
        }

        if (this.fireEvent('beforeUnlock', this) === false) {
            return false;
        }

        if (!callback) callback = this.onUnlockSuccess;

        Ext.Ajax.request({
            url: Phlexible.Router.generate('elements_locks_unlock'),
            params: {
                id: this.lockinfo.id
            },
            success: callback,
            scope: this
        });

        return true;
    },

    // private
    onUnlockSuccess: function (response) {
        var data = Ext.decode(response.responseText);

        if (data.success) {
            this.setStatusIdle();
            Phlexible.success('Lock released.');
        }
        else {
            this.setStatusIdle();
            Ext.MessageBox.alert('Failure', data.msg);
        }
    }
});

Phlexible.element.Teaser = function (config) {
    this.addEvents(
        'beforeLoad',
        'load'
    );

    if (config.siterootId) {
        this.siterootId = config.siterootId;
    }
    if (config.language) {
        this.language = config.language;
    }
};
Ext.extend(Phlexible.element.Teaser, Ext.util.Observable, {
    siterootId: '',
    id: '',
    eid: '',
    language: '',
    version: '',
    master: false,
    title: '',
    properties: null,
    loaded: false,

    load: function (eid, version, language) {
        if (!language) {
            language = this.language;
        }

        var loadParams = {
            siterootId: this.siterootId,
            eid: eid,
            language: language
        };
        if (version) {
            loadParams.version = version;
        } else {
            loadParams.version = null;
        }

        this.reload(loadParams);
    },

    reload: function (params) {
        if (!params) {
            params = {};
        }

        if (!params.siterootId) {
            params.siterootId = this.siterootId;
        }
        if (params.eid === undefined) {
            params.eid = this.eid;
        }
        if (params.language === undefined) {
            params.language = this.language;
        }
        if (params.version !== null && params.version === undefined && this.version) {
            params.version = this.version;
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('elements_data_load'),
            params: params,
            success: this.onLoadSuccess,
            failure: function () {
                alert("failure");
            },
            scope: this
        });
    },

    onLoadSuccess: function (response) {
        var elementData = Ext.decode(response.responseText);
        var properties = elementData.properties;

        this.data = elementData;
        this.properties = properties;

        this.master = properties.master;
        this.tid = properties.tid;
        this.eid = properties.eid;
        this.language = properties.language;
        this.version = properties.version;
        this.title = properties.title;
        this.masterlanguage = properties.masterlanguage;

        this.setLockStatus(elementData.lockinfo);

        this.loaded = true;

        this.fireEvent('load', this);
    },

    showNewTeaserWindow: function (node, element_type_id) {
        /*
         if(!node) {
         node = this.getTreeNode();
         }
         var w = new Phlexible.element.NewElementWindow({
         element_type_id: element_type_id,
         submitParams: {
         siterootId: this.siterootId,
         eid: node.attributes.eid,
         id: node.id
         },
         listeners: {
         success: function(node) {
         node.attributes.children = false;
         node.reload();
         }.createDelegate(this, [node], false)
         }
         });
         w.show();
         */
    },

    getLockStatus: function () {
        if (this.lockinfo && (this.lockinfo.status == 'edit' || this.lockinfo.status == 'locked')) {
            return this.lockinfo.status;
        }

        return 'idle';
    },

    // protected
    setLockStatus: function (lockinfo) {
        if (!lockinfo) {
            this.setStatusIdle();
            return;
        }

        this.lockinfo = lockinfo;

        switch (lockinfo.status) {
            case 'edit':
                this.setStatusEdit();
                break;

            case 'locked':
                this.setStatusLocked();
                break;

            case 'locked_permanently':
                this.setStatusLockedPermanently();
                break;

            default:
                this.setStatusIdle();
        }
    },

    // protected
    setStatusLocked: function () {
        if (!this.lockinfo) this.lockinfo = {};

        this.lockinfo.status = 'locked';

        this.fireEvent('islocked', this);
    },

    // protected
    setStatusEdit: function () {
        if (!this.lockinfo) this.lockinfo = {};

        this.lockinfo.status = 'edit';

        this.fireEvent('getlock', this);
    },

    // protected
    setStatusIdle: function () {
        this.lockinfo = null;

        this.fireEvent('removelock', this);
    },

    lock: function (callback) {
        if (this.lockinfo) {
            return false;
        }

        if (this.fireEvent('beforeLock', this) === false) {
            return false;
        }

        if (!callback) callback = this.onLockSuccess;

        Ext.Ajax.request({
            url: Phlexible.Router.generate('elements_locks_lock'),
            params: {
                eid: this.eid,
                language: this.language
            },
            success: callback,
            scope: this
        });

        return true;
    },

    // private
    onLockSuccess: function (response) {
        var data = Ext.decode(response.responseText);

        if (data.success) {
            this.setStatusEdit();
            Phlexible.success('Lock acquired.');
        }
        else {
            this.setStatusLocked();
            Ext.MessageBox.alert('Failure', data.msg);
        }
    },

    unlock: function (callback) {
        if (!this.lockinfo || this.lockinfo.status != 'edit') {
            return false;
        }

        if (this.fireEvent('beforeUnlock', this) === false) {
            return false;
        }

        if (!callback) callback = this.onUnlockSuccess;

        Ext.Ajax.request({
            url: Phlexible.Router.generate('elements_locks_unlock'),
            params: {
                id: this.lockinfo.id
            },
            success: callback,
            scope: this
        });

        return true;
    },

    // private
    onUnlockSuccess: function (response) {
        var data = Ext.decode(response.responseText);

        if (data.success) {
            this.setStatusIdle();
            Phlexible.success('Lock released.');
        }
        else {
            this.setStatusIdle();
            Ext.MessageBox.alert('Failure', data.msg);
        }
    }
});
