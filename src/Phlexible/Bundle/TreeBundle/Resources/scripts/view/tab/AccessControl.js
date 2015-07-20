Ext.provide('Phlexible.tree.view.tab.AccessControl');

Ext.require('Phlexible.accesscontrol.RightsGrid');

Phlexible.tree.view.tab.AccessControl = Ext.extend(Phlexible.accesscontrol.RightsGrid, {
    languageEnabled: false,
    element: null,
    title: Phlexible.elements.Strings.access_control,
    iconCls: 'p-element-tab_rights-icon',
    right: 'ACCESS',
    objectType: 'Phlexible\\Bundle\\TreeBundle\\Node\\NodeContext',
    strings: {
        users: Phlexible.elements.Strings.users,
        user: Phlexible.elements.Strings.user,
        groups: Phlexible.elements.Strings.groups,
        group: Phlexible.elements.Strings.group
    },

    createIconCls: function(permission) {
        return 'p-element-' + permission.name.toLowerCase() + '-icon';
    },

    initComponent: function () {
        this.urls = {
            identities: Phlexible.Router.generate('elements_rights_identities'),
            add: Phlexible.Router.generate('elements_rights_add')
        };

        this.element.on({
            load: this.onLoadElement,
            getlock: this.onGetLock,
            islocked: this.onIsLocked,
            removelock: this.onRemoveLock,
            scope: this
        });

        this.on({
            show: function () {
                this.lazyLoad(this.element.getNodeId(), this.element.getTeaserId());
            },
            scope: this
        });

        Phlexible.tree.view.tab.AccessControl.superclass.initComponent.call(this);
    },

    getLanguageData: function () {
        var languageData = Phlexible.clone(Phlexible.Config.get('set.language.frontend'));
        languageData.unshift(['_all_', this.strings.all, 'p-contentrights-all-icon']);

        return languageData;
    },

    onLoadElement: function (element) {
        if (!this.hidden) {
            this.lazyLoad(element.getNodeId(), element.getTeaserId());
        }
    },

    lazyLoad: function (nodeId, teaserId) {
        var objectType, objectId;

        if (teaserId && teaserId !== undefined) {
            objectType = 'Phlexible\\Bundle\\TeaserBundle\\Teaser\\TeaserContext';
            objectId = teaserId;
        }
        else {
            objectType = 'Phlexible\\Bundle\\TreeBundle\\Node\\NodeContext';
            objectId = nodeId;
        }

        this.doLoad(objectType, objectId);
    },

    onGetLock: function (element) {
        if (this.right && element.isGranted(this.right)) {
            this.disable();
        }
        else {
            this.enable();
        }
    },

    onIsLocked: function () {
        this.disable();
    },

    onRemoveLock: function () {
        this.disable();
    }
});

Ext.reg('tree-tab-accesscontrol', Phlexible.tree.view.tab.AccessControl);
