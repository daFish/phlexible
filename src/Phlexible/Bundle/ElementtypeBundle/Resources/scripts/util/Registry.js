Ext.define('Phlexible.elementtype.util.Registry', {
    factories: {},

    /**
     * @param {String}   key
     * @param {Function} fn
     */
    register: function (key, fn) {
        if (this.has(key)) {
            throw new Error("Field factory " + key + ' already registered.');
        }

        this.factories[key] = fn;

        Phlexible.Logger.debug('Registered field factory ' + key);
    },

    /**
     * @param {String} key
     * @returns {Boolean}
     */
    has: function (key) {
        return !!this.factories[key];
    },

    /**
     * @param {String} key
     * @returns {Object}
     */
    get: function (key) {
        if (!this.has(key)) {
            throw new Error("Field factory " + key + ' not registered.');
        }

        return this.factories[key];
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
