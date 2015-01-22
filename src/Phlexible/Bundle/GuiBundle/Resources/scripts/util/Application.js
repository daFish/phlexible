/**
 * Application class
 */
Ext.define('Phlexible.gui.util.Application', {
    extend: 'Ext.util.Observable',
    requires: [
        'Ext.util.Observable',
        'Ext.Viewport',
        'Phlexible.gui.util.Config',
        'Phlexible.gui.util.Poller',
        'Phlexible.gui.util.RequestListener'
    ],

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
        Phlexible.console.group('Application.init()');

        this.initConfig();

        this.initUser();

        this.initMenu();

        // initialize request listener
        this.initRequestListener();

        this.initPanels();

        // generate viewport
        this.initViewport();

        this.fireEvent('guiready', this);

        // remove splashscreen
        this.removeSplash();

        Phlexible.console.groupEnd();
    },

    initConfig: function() {
        Phlexible.console.debug('Application.initConfig()');

        var configValues = this.config;
        this.config = Ext.create('Phlexible.gui.util.Config', configValues);
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
     * @private
     */
    initUser: function() {
        Phlexible.console.debug('Application.initUser()');

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
     * Return user
     *
     * @return {Phlexible.gui.util.User}
     */
    getUser: function() {
        return this.user;
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
     * @private
     */
    initMenu: function () {
        this.menu = Ext.create('Phlexible.gui.menu.Menu', {
            menuData: Phlexible.menu
        });
        this.tray = Ext.create('Phlexible.gui.menu.Tray', {
        });
    },

    /**
     * @private
     */
    initRequestListener: function() {
        Phlexible.console.debug('Application.initRequestListener()');

        var requestListener = Ext.create('Phlexible.gui.util.RequestListener');
        requestListener.bind(this, Ext.Ajax);
    },

    /**
     * @private
     */
    removeSplash: function() {
        Phlexible.console.debug('Application.removeSplash()');

        Ext.get("loading").fadeOut({remove: true});
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
     * Return menu bar
     *
     * @returns {Phlexible.gui.menu.MenuBar}
     */
    getMenuBar: function() {
        return this.getMainPanel().getDockedComponent('menuBar');
    },

    /**
     * Return tray
     *
     * @returns {Phlexible.gui.menu.Tray}
     */
    getTray: function() {
        return this.tray;
    },

    /**
     * Return menu
     *
     * @returns {Phlexible.gui.menu.Menu}
     */
    getMenu: function() {
        return this.menu();
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
     * @private
     */
    initPoller: function() {
        Phlexible.console.debug('Application.initPoller()');

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
     * @private
     */
    initViewport: function() {
        Phlexible.console.debug('Application.initViewport()');

        var dockedItems = [];

        if (!this.noMenu) {
            dockedItems.push({
                xtype: 'menu-menubar',
                itemId: 'menuBar',
                menu: this.menu,
                tray: this.tray
            });
        }

        this.viewport = Ext.create('Ext.Viewport', {
            layout: 'fit',
            items: [{
                xtype: 'tabpanel',
                itemId: 'mainPanel',
                border: false,
                tabPosition: 'bottom',
                enableTabScroll: true,
                dockedItems: dockedItems,
                items: this.initialPanels
            }]
        });
        delete this.initialPanels;
    },

    /**
     * @private
     */
    initPanels: function() {
        Phlexible.console.debug('Application.initPanels()');
        this.initialPanels = [];
        return;

        var menuitems = [];
        //menuitems.push({item: 'Phlexible.dashboard.menuitem.Dashboard', parameters: {}});

        if (this.panels) {
            if (Ext.isArray(this.panels)) {
                Ext.each(this.panels, function(panel) {
                    menuitems.push(panel);
                });
            }
            delete this.panels;
        }

        Phlexible.console.debug('Application.loadInititalPanels()', menuitems);

        Ext.each(menuitems, function(menuitem) {
            var item = Ext.create(menuitem.item, menuitem.parameters);
            item.handle();
        });
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
            uri = Routing.generate('phlx_gui_index');

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
