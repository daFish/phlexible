Ext.define('Phlexible.elementtype.util.Types', {
    types: {},

    /**
     * @param {Object} fieldConfig
     */
    register: function (fieldConfig) {
        if (!fieldConfig.type) {
            throw new Error("Missing type in field type: " + JSON.stringify(fieldConfig));
        }

        if (this.has(fieldConfig.type)) {
            throw new Error("Field type " + fieldConfig.type + ' aleady registered.');
        }

        this.types[fieldConfig.type] = fieldConfig;

        Phlexible.Logger.debug('Registered field type ' + fieldConfig.type);
    },

    /**
     * @param {String} type
     * @returns {Object}
     */
    get: function (type) {
        if (!this.has(type)) {
            throw new Error("Field type " + type + " not registered.");
        }

        return this.types[type];
    },

    /**
     * @param {String} type
     * @returns {Boolean}
     */
    has: function (type) {
        return !!this.types[type];
    },

    /**
     * @param {Function} fn
     * @param {Function} scope
     */
    each: function(fn, scope) {
        Ext.Object.each(this.types, fn, scope);
    }
});
