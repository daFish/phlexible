Ext.define('Phlexible.gui.util.Handles', {
    /**
     * @param {String} key
     * @returns {String|Function}
     */
    get: function(key) {
        if (!this.has(key)) {
            throw new Error("Unknown menu item " + key);
        }

        return Phlexible.Storage.get('menu', key);
    },

    /**
     * @param {String} key
     * @returns {Boolean}
     */
    has: function(key) {
        return Phlexible.Storage.contains('menu', key);
    },

    /**
     * @deprecated
     */
    add: function(key) {
        throw new Error("Phlexible.gui.util.Handles.add(" + key + ") was called.");
    }
});

