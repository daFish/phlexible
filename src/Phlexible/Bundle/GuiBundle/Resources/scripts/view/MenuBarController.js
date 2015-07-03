Ext.define('Phlexible.gui.view.MenuBarController', {
    extend: 'Ext.app.ViewController',

    alias: 'controller.gui.menubar',

    init: function() {
        Phlexible.Logger.debug('MenuBarController.initComponent()');

        if (Phlexible.menu) {
            this.populate(Phlexible.menu);
        } else {
            this.load();
        }

        this.callParent(arguments);
    },

    /**
     * Load the menu entries
     *
     * Fires the "beforeload" event before loading the menu, can be canceled.
     * After successful load the "load" event is fired.
     */
    load: function () {
        Phlexible.Logger.debug('MenuBarController.load()');

        Ext.Ajax.request({
            url: Phlexible.Router.generate('phlexible_gui_menu'),
            success: this.onLoadSuccess,
            failure: function () {
                Phlexible.Notify.failure('Error loading menu.');
            },
            scope: this
        });
    },

    /**
     * @see load()
     */
    reload: function () {
        this.load();
    },

    /**
     * Called after successful load
     * @param {Object} response
     * @private
     */
    onLoadSuccess: function (response) {
        var data = Ext.decode(response.responseText);

        this.populate(data);
    },

    /**
     * @param {Array} data
     * @private
     */
    populate: function(data) {
        Phlexible.Logger.debug('MenuBarController.populate()');

        this.loaded = true;

        this.populateMenu(this.iterate(data));
    },

    /**
     * @param {Array} items
     * @private
     */
    populateMenu: function(items) {
        this.getView().items.each(function(item) {
            if (item.itemId !== 'tray') {
                item.destroy();
            }
        });

        var i = 0;

        Ext.each(items, function(item) {
            this.getView().insert(i, item);
            i += 1;
        }, this);
    },

    /**
     * Add a tray item
     *
     * @param {String} itemId
     * @param {Object} config
     */
    addTrayItem: function(itemId, config) {
        Phlexible.Logger.debug('MenuBarController.addTrayItem('+itemId+')');
        config.itemId = itemId;

        if (this.getView().rendered) {
            this.getView().getComponent('tray').add(config);
            this.getView().getComponent('tray').show();
        }
    },

    /**
     * @param {String} itemId
     * @return {Ext.Component}
     * @private
     */
    getTrayItem: function(itemId) {
        return this.getView().getComponent('tray').getComponent(itemId);
    },

    /**
     * @param {String} itemId
     * @return {Boolean}
     * @private
     */
    hasTrayItem: function(itemId) {
        return this.getView().getComponent('tray').getComponent(itemId);
    },

    /**
     * Update a tray item
     *
     * @param {String} itemId
     * @param {Object} config
     */
    updateTrayItem: function(itemId, config) {
        Phlexible.Logger.debug('MenuBarController.updateTrayItem('+itemId+', '+Ext.encode(config)+')');

        var btn = this.getTrayItem(itemId);
        if (config.iconCls) {
            btn.setIconCls(config.iconCls);
        }
        if (config.tooltip) {
            btn.setTooltip(config.tooltip);
        }
        if (config.handler) {
            btn.setHandler(config.handler);
        }
    },

    /**
     * @returns {Object}
     */
    getTrayItems: function() {
        return this.trayItems;
    },

    /**
     * @param {Array} data
     * @returns {Array}
     * @private
     */
    iterate: function (data) {
        var items = [];

        Ext.each(data, function (dataItem) {
            var handleName, handler, config;

            if (!Phlexible.Handles.has(dataItem.handle)) {
                Phlexible.Logger.error('Invalid handle in: ', dataItem);
                return;
            }

            if (dataItem.roles) {
                var allowed = false;
                Ext.each(dataItem.roles, function(role) {
                    if (User.isGranted(role)) {
                        allowed = true;
                        return false;
                    }
                });
                if (!allowed) {
                    return;
                }
            }

            handleName = Phlexible.Handles.get(dataItem.handle);
            if (Ext.isFunction(handleName)) {
                handler = handleName();
            } else {
                handler = Ext.create(handleName);
            }

            if (dataItem.parameters) {
                dataItem.setParameters(dataItem.parameters);
            }

            config = handler.createConfig(dataItem);

            if (Ext.isArray(config)) {
                Ext.each(config, function (configItem) {
                    items.push(configItem);
                }, this);
            } else {
                items.push(config);
            }
        }, this);

        return items;
    }
});
