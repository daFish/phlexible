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
                xtype: 'checkbox',
                name: 'navigation',
                hideLabel: true,
                boxLabel: this.strings.configuration.in_navigation
            },{
                xtype: 'textfield',
                name: 'template',
                fieldLabel: this.strings.configuration.template
            },{
                xtype: 'checkbox',
                name: 'robotsNoIndex',
                fieldLabel: this.strings.configuration.robots,
                boxLabel: this.strings.configuration.robots_no_index
            },{
                xtype: 'checkbox',
                name: 'robotsNoFollow',
                hideLabel: true,
                boxLabel: this.strings.configuration.robots_no_follow
            },{
                xtype: 'checkbox',
                name: 'searchNoIndex',
                fieldLabel: this.strings.configuration.internal_search,
                boxLabel: this.strings.configuration.search_no_index
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
                xtype: 'textfield',
                name: 'methods',
                fieldLabel: this.strings.routing.methods,
                width: 300
            },{
                xtype: 'checkbox',
                name: 'https',
                labelSeparator: this.strings.routing.scheme,
                boxLabel: this.strings.routing.https
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
        if (element.getElementtypeType() !== 'full' && element.getElementtypeType() !== 'part') {
            this.disable();
            //this.hide();

            this.getForm().reset();

            return;
        }

        this.enable();
        //this.show();

        this.getForm().reset();

        var configuration = element.getConfiguration(),
            routing = configuration.routing,
            cache = configuration.cache,
            security = configuration.security;

        if (security && security.roles) {
            var store = this.getComponent(3).getComponent(1).getStore(),
                roles = [];
            Ext.each (security.roles, function(role) {
                roles.push({role: role, name: role});
            }, this);
            store.loadData(roles);
        }

        this.getForm().setValues({
            navigation: configuration.navigation || false,
            template: configuration.template || '',
            robotsNoIndex: configuration.robotsNoIndex || false,
            robotsNoFollow: configuration.robotsNoFollow || false,
            searchNoIndex: configuration.searchNoIndex || false,

            name: routing.name || false,
            path: routing.path || false,
            defaults: routing.defaults || false,
            methods: routing.methods || false,
            https: routing.https || false,
            controller: routing.controller || false,

            expires: cache.authenticationRequired || false,
            public: cache.public || false,
            maxage: cache.maxage || false,
            smaxage: cache.smaxage || false,
            lastModified: cache.lastModified || false,
            ETag: cache.ETag || false,
            vary: cache.vary || false,

            authenticationRequired: security.authenticationRequired || false,
            roles: security.roles || false,
            checkAcl: security.checkAcl || false,
            expression: security.expression || false,
        });

        if (element.getElementtypeType() === 'part') {
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
            parameters = {};

        parameters.template = values.template;
        if (this.element.getElementtypeType() !== 'part') {
            parameters.navigation = values.navigation;
            parameters.robotsNoIndex = values.robotsNoIndex;
            parameters.robotsNoFollow = values.robotsNoFollow;
            parameters.searchNoIndex = values.searchNoIndex;

            parameters.routing = {
                name: values.name,
                path: values.path,
                defaults: values.defaults,
                methods: values.methods,
                https: values.https,
                controller: values.controller
            };
            parameters.cache = {
                expires: values.expires,
                public: values.public,
                maxage: values.maxage,
                smaxage: values.smaxage,
                lastModified: values.lastModified,
                ETag: values.ETag,
                vary: values.vary
            };
            parameters.security = {
                authenticationRequired: values.authenticationRequired,
                roles: values.roles,
                checkAcl: values.checkAcl,
                expression: values.expression
            };
        }

        return parameters;
    }
});

Ext.reg('tree-accordion-configuration', Phlexible.tree.view.accordion.Configuration);
