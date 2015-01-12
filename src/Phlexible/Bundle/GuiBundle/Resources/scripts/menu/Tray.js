/**
 * Tray
 */
Ext.define('Phlexible.gui.menu.Tray', {
    extend: 'Ext.util.Observable',

    items: {},

    add: function(id, config) {
        Phlexible.console.debug('Tray.add()', id, config);

        config.itemId = id;

        this.fireEvent('addItem', config);

        this.items[id] = config.item;

        return this.items[id];
    },

    get: function(id) {
        Phlexible.console.debug('Tray.get(' + id + ')');

        return this.items[id];
    },

    remove: function(btn) {
        // TODO implement
    }
});
