Ext.define('Phlexible.elementtype.util.Types', {
    types: {},

    /**
     * @deprecated
     */
    register: function () {
        throw new Error("Phlexible.elementtype.util.Types.register() called.");
    },

    /**
     * @param {String} type
     * @returns {Object}
     */
    get: function (type) {
        if (!this.has(type)) {
            throw new Error("Field type " + type + " not registered.");
        }

        return Phlexible.PluginManager.get('type', type);
    },

    /**
     * @param {String} type
     * @returns {Boolean}
     */
    has: function (type) {
        return Phlexible.PluginManager.contains('type', type);
    },

    /**
     * @param {Function} fn
     * @param {Function} scope
     */
    each: function(fn, scope) {
        Ext.Object.each(this.types, fn, scope);
    }
});
