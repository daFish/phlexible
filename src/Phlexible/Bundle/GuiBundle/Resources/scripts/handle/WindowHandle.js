Ext.define('Phlexible.gui.menuhandle.handle.WindowHandle', {
    extend: 'Phlexible.gui.menuhandle.handle.Handle',

    /**
     * @cfg {String} name
     */

    /**
     * @cfg {Object} parameters
     */

    /**
     * Return window
     *
     * @return {String}
     */
    getName: function () {
        return this.name;
    },

    getParameters: function () {
        return this.parameters || {};
    },

    setParameters: function (parameters) {
        this.parameters = parameters;
    },

    handle: function () {
        Phlexible.console.debug('WindowHandle.handle(' + this.getName() + ')', this.getParameters());

        var win = Ext.create(this.getName(), this.getParameters());
        win.show();
    }
});