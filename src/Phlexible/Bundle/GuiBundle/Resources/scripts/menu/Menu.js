/**
 * @class Phlexible.gui.MenuBar
 * @extends Ext.util.Observable
 * This class represents the menu.
 * <br><br>Usage:<pre><code>
 var menu = new Phlexible.gui.Menu();
 // ...
 // reload menu
 menu.reload();
 * </code></pre>
 * @constructor
 */
Ext.define('Phlexible.gui.menu.Menu', {
    extend: 'Ext.util.Observable',

    /**
     * Fires before menu is loaded.
     *
     * @event beforeload
     * @param {Phlexible.gui.Menu} menu
     */

    /**
     * Fires after menu is loaded.
     *
     * @event load
     * @param {Phlexible.gui.Menu} menu
     * @param {Array} menuItems
     */

    /**
     * Fires before the menu is populated.
     *
     * @event beforepopulate
     * @param {Phlexible.gui.Menu} menu
     */

    /**
     * Fires after the menu is populated.
     *
     * @event populate
     * @param {Phlexible.gui.Menu} menu
     * @param {Array} menuItems
     */

    /**
     * Fires after a tray item was added.
     *
     * @event addTrayItem
     * @param {Phlexible.gui.Menu} menu
     * @param {Object} config
     */

    /**
     * Fires after a tray item was updated.
     *
     * @event updateTrayItem
     * @param {Phlexible.gui.Menu} menu
     * @param {Object} config
     */

    /**
     * Constructor
     */
    constructor: function(config) {
        this.callParent(arguments);

        if (config.menuData) {
            this.populate(config.menuData);
        } else {
            this.load();
        }
    },

    items: [],
    trayItems: {},

    loaded: false,

    /**
     * Load the menu entries
     *
     * Fires the "beforeload" event before loading the menu, can be canceled.
     * After successful load the "load" event is fired.
     */
    load: function () {
        if (this.fireEvent('beforeload', this) === false) {
            return;
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('gui_menu'),
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

        this.fireEvent('load', this, this.items);
    },

    /**
     * @param {Array} data
     * @private
     */
    populate: function(data) {
        this.fireEvent('beforepopulate', this, data);

        this.items = this.iterate(data);

        this.loaded = true;

        this.fireEvent('populate', this, this.items);
    },

    /**
     * @return {Array}
     */
    getItems: function () {
        return this.items;
    },

    /**
     * Add a tray item
     *
     * @param {String} itemId
     * @param {Object} config
     */
    addTrayItem: function(itemId, config) {
        Phlexible.Logger.debug('Menu.addTrayItem('+itemId+', '+Ext.encode(config)+')');
        config.itemId = itemId;

        this.trayItems[itemId] = config;

        this.fireEvent('addTrayItem', this, config);
    },

    /**
     * Update a tray item
     *
     * @param {String} itemId
     * @param {Object} config
     */
    updateTrayItem: function(itemId, config) {
        Phlexible.Logger.debug('Menu.updateTrayItem('+itemId+', '+Ext.encode(config)+')');
        Ext.apply(this.trayItems[itemId], config);

        this.fireEvent('updateTrayItem', this, this.trayItems[itemId]);
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
