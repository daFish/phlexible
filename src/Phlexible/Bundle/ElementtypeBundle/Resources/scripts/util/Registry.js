Ext.define('Phlexible.elementtype.util.Registry', {
    factories: {},

    /**
     * @deprecated
     */
    register: function () {
        throw new Error("Phlexible.elementtype.util.Registry.register() called.");
    },

    /**
     * @param {String} key
     * @returns {Boolean}
     */
    has: function (key) {
        return Phlexible.Storage.contains('field', key);
    },

    /**
     * @param {String} key
     * @returns {Object}
     */
    get: function (key) {
        if (!this.has(key)) {
            throw new Error("Field factory " + key + ' not registered.');
        }

        return Phlexible.Storage.get('field', key);
    },

    /**
     * @param {String} key
     * @returns {Boolean}
     * @deprecated
     */
    hasFactory: function (key) {
        return this.has(key);
    },

    /**
     * @param {String} key
     * @returns {Object}
     * @deprecated
     */
    getFactory: function (key) {
        return this.get(key);
    }
});
