Ext.provide('Phlexible.tree.view.accordion.Configuration');

Phlexible.tree.view.accordion.Configuration = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.elements.Strings,
    title: Phlexible.elements.Strings.configuration.configuration,
    tabTip: Phlexible.elements.Strings.configuration.configuration,
    cls: 'p-tree-configuration',
    iconCls: 'p-element-action-icon',
    autoHeight: true,
    labelWidth: 100,
    labelAlign: 'top',
    layout: 'accordion',

    key: 'configuration',

    initComponent: function () {
        this.element.on({
            load: this.onLoadElement,
            scope: this
        });

        this.populateItems();

        Phlexible.tree.view.accordion.Configuration.superclass.initComponent.call(this);
    },

    populateItems: function () {
        this.items = [{
            xtype: 'panel',
            layout: 'form',
            title: this.strings.configuration.configuration,
            iconCls: 'p-element-action-icon',
            autoHeight: true,
            bodyStyle: 'padding: 5px',
            items: [{
                xtype: 'textfield',
                name: 'title',
                fieldLabel: this.strings.title,
                width: 300
            },{
                xtype: 'textfield',
                name: 'navigation_title',
                fieldLabel: this.strings.navigation_title,
                width: 300
            },{
                xtype: 'textfield',
                name: 'backend_title',
                fieldLabel: this.strings.backend_title,
                width: 300
            },{
                xtype: 'textfield',
                name: 'slug',
                fieldLabel: this.strings.slug,
                width: 300
            },{
                xtype: 'checkbox',
                name: 'hidden',
                hideLabel: true,
                boxLabel: this.strings.configuration.hidden
            },{
                xtype: 'checkbox',
                name: 'hiddenInNavigation',
                hideLabel: true,
                boxLabel: this.strings.configuration.hidden_in_navigation
            },{
                xtype: 'checkbox',
                name: 'searchNoIndex',
                hideLabel: true,
                boxLabel: this.strings.configuration.search_no_index
            },{
                xtype: 'textfield',
                name: 'template',
                fieldLabel: this.strings.configuration.template,
                width: 300
            }]
        },{
            xtype: 'panel',
            layout: 'form',
            title: this.strings.meta.meta,
            iconCls: 'p-metaset-component-icon',
            autoHeight: true,
            bodyStyle: 'padding: 5px',
            items: [{
                xtype: 'textarea',
                name: 'description',
                fieldLabel: this.strings.meta.description,
                width: 300
            },{
                xtype: 'textfield',
                name: 'keywords',
                fieldLabel: this.strings.meta.keywords,
                width: 300
            },{
                xtype: 'checkbox',
                name: 'robotsNoFollow',
                hideLabel: true,
                boxLabel: this.strings.meta.robots_no_follow
            },{
                xtype: 'checkbox',
                name: 'robotsNoIndex',
                hideLabel: true,
                boxLabel: this.strings.meta.robots_no_index
            }]
        },{
            xtype: 'panel',
            layout: 'form',
            title: this.strings.routing.routing,
            iconCls: 'p-element-routing-icon',
            autoHeight: true,
            bodyStyle: 'padding: 5px',
            items: [{
                xtype: 'textfield',
                name: 'name',
                fieldLabel: this.strings.routing.name,
                width: 300
            },{
                xtype: 'textfield',
                name: 'path',
                fieldLabel: this.strings.routing.path,
                width: 300
            },{
                xtype: 'textfield',
                name: 'defaults',
                fieldLabel: this.strings.routing.defaults,
                width: 300
            },{
                xtype: 'lovcombo',
                name: 'methods',
                fieldLabel: this.strings.routing.methods,
                store: new Ext.data.SimpleStore({
                    fields: ['key', 'name'],
                    data: [['GET', 'GET'], ['HEAD', 'HEAD'], ['POST', 'POST'], ['PUT', 'PUT'], ['DELETE', 'DELETE']]
                }),
                mode: 'local',
                hideOnSelect: false,
                displayField: 'name',
                valueField: 'key',
                triggerAction: 'all',
                editable: false,
                selectOnFocus: true,
                width: 280
            },{
                xtype: 'lovcombo',
                name: 'schemes',
                fieldLabel: this.strings.routing.schemes,
                store: new Ext.data.SimpleStore({
                    fields: ['key', 'name'],
                    data: [['http', 'http'], ['https', 'https']]
                }),
                mode: 'local',
                hideOnSelect: false,
                displayField: 'name',
                valueField: 'key',
                triggerAction: 'all',
                editable: false,
                selectOnFocus: true,
                width: 280
            }, {
                xtype: 'textfield',
                name: 'controller',
                fieldLabel: this.strings.routing.controller,
                width: 300
            }]
        },{
            xtype: 'panel',
            layout: 'form',
            title: this.strings.cache.cache,
            iconCls: 'p-element-cache-icon',
            autoHeight: true,
            bodyStyle: 'padding: 5px',
            items: [{
                xtype: 'textfield',
                name: 'expires',
                fieldLabel: this.strings.cache.expires,
                width: 300
            },{
                xtype: 'checkbox',
                name: 'public',
                hideLabel: true,
                boxLabel: this.strings.cache.public
            },{
                xtype: 'numberfield',
                name: 'maxage',
                fieldLabel: this.strings.cache.maxage,
                width: 300
            },{
                xtype: 'numberfield',
                name: 'smaxage',
                fieldLabel: this.strings.cache.smaxage,
                width: 300
            },{
                xtype: 'textfield',
                name: 'lastModified',
                fieldLabel: this.strings.cache.last_modified,
                width: 300
            },{
                xtype: 'textfield',
                name: 'ETag',
                fieldLabel: this.strings.cache.etag,
                width: 300
            },{
                xtype: 'textfield',
                name: 'vary',
                fieldLabel: this.strings.cache.vary,
                width: 300
            }]
        },{
            xtype: 'panel',
            layout: 'form',
            title: this.strings.security.security,
            iconCls: 'p-element-security-icon',
            autoHeight: true,
            bodyStyle: 'padding: 5px',
            items: [{
                xtype: 'checkbox',
                name: 'authenticationRequired',
                hideLabel: true,
                boxLabel: this.strings.security.authentication_required
            },{
                xtype: 'superboxselect',
                fieldLabel: this.strings.security.required_roles,
                width: 300,
                listWidth: 300,
                name: 'roles',
                store: new Ext.data.JsonStore({
                    fields: ['role', 'name'],
                    id: 'role'
                }),
                displayField: 'name',
                valueField: 'role',
                mode: 'local',
                allowAddNewData: true,
                stackItems: true,
                pinList: false,
                listeners: {
                    newitem: function (bs, v) {
                        var newObj = {
                            role: v
                        };
                        bs.addNewItem(newObj);
                    }
                }
            },{
                xtype: 'checkbox',
                name: 'checkAcl',
                hideLabel: true,
                boxLabel: this.strings.security.check_acl
            },{
                xtype: 'textfield',
                name: 'expression',
                fieldLabel: this.strings.security.expression,
                width: 300
            }]
        }];
    },

    onLoadElement: function (element) {
        if (element.getTreeNode().attributes.type !== 'page' && element.getTreeNode().attributes.type !== 'part') {
            this.disable();
            //this.hide();

            this.getForm().reset();

            return;
        }

        this.enable();
        //this.show();

        this.getForm().reset();

        var configuration = element.getConfiguration(),
            routing = configuration.routing || {},
            cache = configuration.cache || {},
            security = configuration.security || {},
            meta = configuration.meta || {};

        if (security && security.roles) {
            var store = this.getComponent(4).getComponent(1).getStore(),
                roles = [];
            Ext.each (security.roles, function(role) {
                roles.push({role: role, name: role});
            }, this);
            store.loadData(roles);
        }

        this.getForm().setValues({
            title: configuration.title || '',
            navigation_title: configuration.navigation_title || '',
            backend_title: configuration.backend_title || '',
            slug: configuration.slug || '',
            hiddenInNavigation: !configuration.navigation,
            template: configuration.template || '',
            searchNoIndex: configuration.searchNoIndex || false,

            name: routing.name || '',
            path: routing.path || '',
            defaults: routing.defaults || '',
            methods: routing.methods || '',
            schemes: routing.schemes || '',
            controller: routing.controller || '',

            expires: cache.authenticationRequired || '',
            public: cache.public || false,
            maxage: cache.maxage || '',
            smaxage: cache.smaxage || '',
            lastModified: cache.lastModified || '',
            ETag: cache.ETag || '',
            vary: cache.vary || '',

            authenticationRequired: security.authenticationRequired || false,
            roles: security.roles || '',
            checkAcl: security.checkAcl || false,
            expression: security.expression || '',

            description: meta.description || '',
            keywords: meta.keywords || '',
            robotsNoIndex: meta.robotsNoIndex || false,
            robotsNoFollow: meta.robotsNoFollow || false
        });

        if (element.getTreeNode().attributes.type === 'part') {
            this.getComponent(0).getComponent(0).hide();
            this.getComponent(0).getComponent(1).show();
            this.getComponent(0).getComponent(2).hide();
            this.getComponent(0).getComponent(3).hide();
            this.getComponent(0).getComponent(4).hide();
            this.getComponent(1).hide();
            this.getComponent(2).hide();
            this.getComponent(3).hide();
        } else {
            this.getComponent(0).getComponent(0).show();
            this.getComponent(0).getComponent(1).show();
            this.getComponent(0).getComponent(2).show();
            this.getComponent(0).getComponent(3).show();
            this.getComponent(0).getComponent(4).show();
            this.getComponent(1).show();
            this.getComponent(2).show();
            this.getComponent(3).show();
        }
    },

    getData: function () {
        if (!this.getForm().isValid()) {
            //errors.push('Required fields are missing.');
            return false;
        }

        var values = this.getForm().getValues(),
            parameters = {},
            routing = {},
            cache = {},
            security = {},
            meta = {};

        parameters.template = values.template;
        if (this.element.getTreeNode().attributes.type !== 'part') {
            parameters.title = values.title;
            if (values.navigation_title) {
                parameters.navigation_title = values.navigation_title;
            }
            if (values.backend_title) {
                parameters.backend_title = values.backend_title;
            }
            if (values.slug) {
                parameters.slug = values.slug;
            }
            if (values.hidden) {
                parameters.hidden = values.hidden;
            }
            if (!values.hiddenInNavigation) {
                parameters.navigation = !values.hiddenInNavigation;
            }
            if (values.searchNoIndex) {
                parameters.searchNoIndex = values.searchNoIndex;
            }

            // meta
            if (values.description) {
                meta.description = values.description;
            }
            if (values.keywords) {
                meta.keywords = values.keywords;
            }
            if (values.robotsNoIndex) {
                meta.robotsNoIndex = values.robotsNoIndex;
            }
            if (values.robotsNoFollow) {
                meta.robotsNoFollow = values.robotsNoFollow;
            }

            // routing
            if (values.name) {
                routing.name = values.name;
            }
            if (values.path) {
                routing.path = values.path;
            }
            if (values.defaults) {
                routing.defaults = values.defaults;
            }
            if (values.methods) {
                routing.methods = values.methods;
            }
            if (values.schemes) {
                routing.schemes = values.schemes;
            }
            if (values.controller) {
                routing.controller = values.controller;
            }

            // cache
            if (values.expires) {
                cache.expires = values.expires;
            }
            if (values.public) {
                cache.public = values.public;
            }
            if (values.maxage) {
                cache.maxage = values.maxage;
            }
            if (values.smaxage) {
                cache.smaxage = values.smaxage;
            }
            if (values.lastModified) {
                cache.lastModified = values.lastModified;
            }
            if (values.ETag) {
                cache.ETag = values.ETag;
            }
            if (values.vary) {
                cache.vary = values.vary;
            }

            // security
            if (values.authenticationRequired) {
                security.authenticationRequired = values.authenticationRequired;
            }
            if (values.roles) {
                security.roles = values.roles;
            }
            if (values.checkAcl) {
                security.checkAcl = values.checkAcl;
            }
            if (values.expression) {
                security.expression = values.expression;
            }

            parameters.meta = meta;
            parameters.routing = routing;
            parameters.cache = cache;
            parameters.security = security;
        }

        return parameters;
    }
});

Ext.reg('tree-accordion-configuration', Phlexible.tree.view.accordion.Configuration);
