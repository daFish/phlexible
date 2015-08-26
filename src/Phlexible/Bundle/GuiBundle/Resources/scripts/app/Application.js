"use strict";

Ext.define('Phlexible.gui.app.Application', {
    extend: 'Ext.app.Application',
    requires: [
        'Phlexible.gui.util.Config',
        'Phlexible.gui.util.Poller',
        'Phlexible.gui.util.RequestListener',
        'Phlexible.gui.util.User',
        'Phlexible.gui.view.MenuBar',
        'Phlexible.gui.view.Main'
    ],
    $configStrict: false,

    name: 'Phlexible',
    namespace: 'Phlexible',
    appFolder: 'gui/load/Phlexible',
    appProperty: 'App',

    listen : {
        controller : {
            '#' : {
                unmatchedroute : 'onUnmatchedRoute'
            }
        }
    },

    init: function() {
        Phlexible.Logger.debug('Application.init()');

        this.initMyConfig();
        this.initMyUser();
        this.initMyRequestListener();
        this.initMyPoller();
    },

    launch: function() {
        Phlexible.Logger.debug('Application.launch()');

        this.requestListener.bind(this, Ext.Ajax);
        this.poller.bind(this);

        if (Phlexible.User.isImpersonated()) {
            this.getMenu().addTrayItem('impersonated', {
                tooltip: 'Switch back to user "' + Phlexible.User.getPreviousUsername() + '"',
                iconCls: Phlexible.Icon.get('user-thief'),
                handler: function() {
                    document.location.href = Phlexible.Router.generate('phlexible_gui', {"_switch_user": "_exit"});
                },
                scope: this
            });
        }

        Ext.get("loading").fadeOut({remove: true});
    },

    getMenu: function() {
        return this.getMainView().getComponent('main').getDockedComponent('menubar').getController();
    },

    getTabs: function() {
        return this.getMainView().getComponent('main');
    },

    getPoller: function() {
        return this.poller;
    },

    onUnmatchedRoute: function(hash) {
        Phlexible.Logger.notice('Handling unmatched route: ' + hash);
        var parts = hash.split('/'),
            name = parts[0],
            id = parts[1],
            handlerName, handler;

        if (Phlexible.Handles.has(name)) {
            handlerName = Phlexible.Handles.get(name);
            handler = Ext.create(handlerName);

            handler.handle();
        }
    },

    /**
     * Add panel by menuitem
     * @param {Phlexible.gui.menu.item.Item} menuitem
     * @param {Boolean} noCloseButton
     * @returns {Ext.Component}
     */
    addPanelByHandle: function(menuitem, noCloseButton) {
        var identifier = menuitem.getIdentifier(),
            xtype      = menuitem.getName(),
            parameters = menuitem.getParameters(),
            panel      = this.getTabs().getComponent(identifier);

        if (!Ext.isString(xtype)) {
            throw new Error('xtype has to be a string.');
        }

        if (!panel) {
            panel = this.getTabs().add({
                xtype: xtype,
                id: identifier,
                header: true,
                closable: !noCloseButton,
                menuitem: menuitem,
                tools: [{
                    type: 'close',
                    handler: function(e, toolEl){
                        this.removePanelByMenuitem(menuitem);
                    },
                    scope: this
                }],
                parameters: parameters
            });
        } else {
            if (panel.loadParameters) {
                panel.loadParameters(parameters);
            }
        }

        this.getTabs().setActiveTab(panel);

        return panel;
    },

    /**
     * Initialize config
     *
     * @private
     */
    initMyConfig: function() {
        Phlexible.Logger.debug('Application.initMyConfig()');

        var config = Phlexible.config;
        delete Phlexible.config;

        Phlexible.Config = Ext.create('Phlexible.gui.util.Config', config);
    },

    /**
     * Initialize user
     *
     * @private
     */
    initMyUser: function() {
        Phlexible.Logger.debug('Application.initMyUser()');

        Phlexible.User = Ext.create('Phlexible.gui.util.User', {
            id: Phlexible.Config.get('user.id'),
            username: Phlexible.Config.get('user.id'),
            email: Phlexible.Config.get('user.email'),
            firstname: Phlexible.Config.get('user.firstname'),
            lastname: Phlexible.Config.get('user.lastname'),
            displayName: Phlexible.Config.get('user.displayName'),
            properties: Phlexible.Config.get('user.properties'),
            roles: Phlexible.Config.get('user.roles'),
            previousUsername: Phlexible.Config.get('user.previousUsername')
        });
        Phlexible.User.getRoles();
    },

    /**
     * Initialize request listener
     *
     * @private
     */
    initMyRequestListener: function() {
        Phlexible.Logger.debug('Application.initMyRequestListener()');

        this.requestListener = Ext.create('Phlexible.gui.util.RequestListener');
    },

    /**
     * @private
     */
    initMyPoller: function() {
        Phlexible.Logger.debug('Application.initMyPoller()');

        var poller = this.poller = Ext.create('Phlexible.gui.util.Poller', {
            noButton: false,
            autoStart: true
        });

        poller.on('message', function(e){
            if (e.msg) {
                Phlexible.Logger.debug('Message: ', e.msg);
            }
        });

        this.fireEvent('initPoller', poller);
    }
});

/**
 * Application class
 */
Ext.define('Phlexible.gui.util.xApplication', {
    extend: 'Ext.util.Observable',

    /**
     * @property {Phlexible.gui.util.RequestListener} requestListener
     * Request listener
     */

    /**
     * @property {Phlexible.gui.menu.Menu} menu
     * Menu
     */

    /**
     * @property {Phlexible.gui.util.Poller} poller
     * Poller
     */

    /**
     * @property {Ext.Viewport} viewport
     * Viewport
     */

    /**
     * @event guiready
     * Fires when gui is ready
     */

    /**
     * Initialize application
     */
    init: function(){
        Phlexible.Logger.debug('Application.init()');

        this.initConfig();
        this.initUser();
        this.initMenu();
        this.initRequestListener();
        this.initPanels();
        this.initPoller();

        Phlexible.Logger.debug('Application > guiready');

        this.fireEvent('guiready', this);

        // remove splashscreen
        this.removeSplash();

        Phlexible.Logger.debug('Application.init() > done');
    },

    /**
     * Return config
     *
     * @return {Phlexible.gui.util.Config}
     */
    getConfig: function() {
        return this.config;
    },

    /**
     * Return menu
     *
     * @returns {Phlexible.gui.view.Menu}
     */
    getMenu: function() {
        return this.menu;
    },

    /**
     * Return user
     *
     * @return {Phlexible.gui.util.User}
     */
    getUser: function() {
        return this.user;
    },

    /**
     * Return poller
     *
     * @returns {Phlexible.gui.util.Poller}
     */
    getPoller: function() {
        return this.poller;
    },

    /**
     * Return viewport
     *
     * @returns {Ext.Viewport}
     */
    getViewport: function() {
        return this.viewport;
    },

    /**
     * Return main panel
     *
     * @returns {Ext.panel.Panel}
     */
    getMainPanel: function() {
        return this.viewport.getComponent('mainPanel');
    },

    /**
     * Return active panel
     *
     * @return {Ext.Component}
     */
    getActivePanel: function(){
        return this.getMainPanel().getActiveTab();
    },

    /**
     * Is the current user granted access to role?
     *
     * @param {String} role
     * @return {Boolean}
     */
    isGranted: function(role) {
        return this.user.isGranted(role);
    },

    /**
     * Initialize config
     *
     * @private
     */
    initConfig: function() {
        Phlexible.Logger.debug('Application.initConfig()');

        var configValues = this.config;
        this.config = Ext.create('Phlexible.gui.util.Config', configValues);
    },

    /**
     * Initialize menu
     *
     * @private
     */
    initMenu: function() {
        this.menu = Extcreate('Phlexible.gui.menu.Menu', {
            menuData: Phlexible.menu
        });
    },

    /**
     * Initialize user
     *
     * @private
     */
    initUser: function() {
        Phlexible.Logger.debug('Application.initUser()');

        this.user = Ext.create('Phlexible.gui.util.User', {
            id: this.config.get('user.id'),
            username: this.config.get('user.id'),
            email: this.config.get('user.email'),
            firstname: this.config.get('user.firstname'),
            lastname: this.config.get('user.lastname'),
            displayName: this.config.get('user.displayName'),
            properties: this.config.get('user.properties'),
            roles: this.config.get('user.roles')
        });
        this.user.getRoles();
    },

    /**
     * Initialize request listener
     *
     * @private
     */
    initRequestListener: function() {
        Phlexible.Logger.debug('Application.initRequestListener()');

        var requestListener = Ext.create('Phlexible.gui.util.RequestListener');
        requestListener.bind(this, Ext.Ajax);
    },

    /**
     * @private
     */
    initPoller: function() {
        Phlexible.Logger.debug('Application.initPoller()');

        var poller = this.poller = Ext.create('Phlexible.gui.util.Poller', {
            noButton: false,
            autoStart: true
        });
        this.poller.on('message', function(e){
            if(e.msg) {
                Phlexible.msg('Event', e.msg);
            }
        });
        Ext.getWin().on('focus', function() {
            poller.start();
        });
        Ext.getWin().on('blur', function() {
            poller.stop();
        });

        this.fireEvent('initPoller', poller);
    },

    /**
     * Initialize viewport
     *
     * @private
     */
    initViewport: function() {
        Phlexible.Logger.debug('Application.initViewport()');

        this.viewport = Ext.create('Ext.Viewport', {
            layout: 'fit',
            items: [{
                xtype: 'tabpanel',
                itemId: 'mainPanel',
                border: false,
                tabPosition: 'bottom',
                enableTabScroll: true,
                dockedItems: [{
                    xtype: 'menu-menubar',
                    itemId: 'menuBar',
                    menu: this.menu
                }],
                items: this.initialPanels
            }]
        });

        delete this.initialPanels;
    },

    /**
     * Initialize start panels
     *
     * @private
     */
    initPanels: function() {
        Phlexible.Logger.debug('Application.initPanels()');
        this.initialPanels = [];

        return;

        var menuitems = [];

        if (this.panels) {
            if (Ext.isArray(this.panels)) {
                Ext.each(this.panels, function(panel) {
                    menuitems.push(panel);
                });
            }
            delete this.panels;
        }

        Phlexible.Logger.debug('Application.loadInititalPanels()', menuitems);

        Ext.each(menuitems, function(menuitem) {
            var item = Ext.create(menuitem.item, menuitem.parameters);
            item.handle();
        });
    },

    /**
     * Remove splash screen
     *
     * @private
     */
    removeSplash: function() {
        Phlexible.Logger.debug('Application.removeSplash()');

        Ext.get("loading").fadeOut({remove: true});
    },

    /**
     * Add panel by menuitem
     * @param {Phlexible.gui.menu.item.Item} menuitem
     * @param {Boolean} noCloseButton
     * @returns {Ext.Component}
     */
    addPanelByHandle: function(menuitem, noCloseButton) {
        var identifier = menuitem.getIdentifier(),
            xtype      = menuitem.getName(),
            parameters = menuitem.getParameters(),
            panel      = this.getMainPanel().getComponent(identifier);

        if (!Ext.isString(xtype)) {
            throw new Error('xtype has to be a string.');
        }

        if (!panel) {
            panel = this.getMainPanel().add({
                xtype: xtype,
                id: identifier,
                header: true,
                closable: !noCloseButton,
                menuitem: menuitem,
                tools: [{
                    type: 'close',
                    handler: function(e, toolEl){
                        this.removePanelByMenuitem(menuitem);
                    },
                    scope: this
                }],
                parameters: parameters
            });
        } else {
            if (panel.loadParameters) {
                panel.loadParameters(parameters);
            }
        }

        this.getMainPanel().setActiveTab(panel);

        return panel;
    },

    /**
     * Return panel by menuitem
     * @param {Phlexible.gui.menu.item.Item} menuitem
     * @returns {Ext.Component}
     */
    getPanelByMenuitem: function(menuitem) {
        var identifier = menuitem.getIdentifier(),
            panel      = this.getMainPanel().getComponent(identifier);

        if (!panel) {
            return null;
        }

        return panel;
    },

    /**
     * Remove panel by menuitem
     * @param {Phlexible.gui.menu.item.Item} menuitem
     */
    removePanelByMenuitem: function(menuitem) {
        var panel = this.getPanelByMenuitem(menuitem);
        if (!panel) {
            return;
        }
        panel.ownerCt.remove(panel);
        panel.destroy();
    },

    /**
     * Return panel query string
     * @return {String}
     */
    getActivePanelsUri: function() {
        var config = [],
            uri = Routing.generate('phlexible_gui');

        this.getMainPanel().items.each(function(item) {
            config.push(item.menuitem.getQueryConfig());
        });

        if (config.length) {
            uri += '?p=' + Ext.encode(config);
        }

        return uri;
    },

    /**
     * Open panel query string
     */
    reloadWithActivePanels: function() {
        document.location.href = this.getActivePanelsUri();
    }
});
