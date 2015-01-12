Ext.define('Phlexible.gui.util.Handles', {
    constructor: function() {
        this.handles = {};
    },
    get: function(key) {
        return this.handles[key];
    },
    has: function(key) {
        return this.handles[key] !== undefined;
    },
    add: function(key, fn) {
        this.handles[key] = fn;
    }
});

