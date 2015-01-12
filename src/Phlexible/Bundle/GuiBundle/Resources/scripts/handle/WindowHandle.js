Ext.define('Phlexible.gui.menuhandle.handle.WindowHandle', {
    extend: 'Phlexible.gui.menuhandle.handle.Handle',

    /**
     * @cfg {String} window
     */

    /**
     * @cfg {Object} parameters
     */

    /**
     * Return window
     *
     * @return {String}
     */
    getWindow: function () {
        return this.window;
    },

    getParameters: function () {
        return this.parameters || {};
    },

    setParameters: function (parameters) {
        this.parameters = parameters;
    },

    handle: function () {
        Phlexible.console.debug('WindowHandle.handle(' + this.getWindow() + ')', this.getParameters());

        var win = Ext.create(this.getWindow(), this.getParameters());
        win.show();
    }
});