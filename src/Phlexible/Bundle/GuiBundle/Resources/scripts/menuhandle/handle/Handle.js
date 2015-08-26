Ext.define('Phlexible.gui.menuhandle.handle.Handle', {
    constructor: function(config) {
        Ext.apply(this, config);
    },

    /**
     * @cfg {String} text Display text
     */
    text: '',

    /**
     * @cfg {String} iconCls Icon class
     */
    iconCls: '',

    /**
     * Return text
     * @return {String}
     */
    getText: function () {
        return this.text;
    },

    /**
     * Return iconCls
     * @return {String}
     */
    getIconCls: function () {
        return this.iconCls;
    },

    /**
     * Handle menu item
     */
    handle: function () {
    },

    /**
     * Create and return config
     *
     * @private
     * @param {Object} data
     * @return {Object}
     */
    createConfig: function (data) {
        var btnConfig = this.createBasicConfig();

        btnConfig.handler = function () {
            this.handle();
        };
        btnConfig.scope = this;

        return btnConfig;
    },

    /**
     * Create and return basic config
     *
     * @private
     * @return {Object}
     */
    createBasicConfig: function () {
        var btnConfig = {
            text: this.getText(),
            iconCls: this.getIconCls()
        };

        return btnConfig;
    }
});