Ext.define('Phlexible.gui.menuhandle.handle.XtypeHandle', {
    extend: 'Phlexible.gui.menuhandle.handle.Handle',

    /**
     * @cfg {String} xtype
     */

    /**
     * @cfg {Object} parameters
     */

    /**
     * Return xtype
     *
     * @return {String}
     */
    getXtype: function () {
        return this.xtype;
    },

    getIdentifier: function () {
        return this.getXtype();
    },

    getParameters: function () {
        return this.parameters || {};
    },

    setParameters: function (parameters) {
        this.parameters = parameters;
    },

    handle: function () {
        Phlexible.console.debug('XtypeHandle.handle(' + this.getXtype() + ', ' + this.getIdentifier() + ')', this.getParameters());

        Phlexible.App.addPanelByHandle(this);
    }
});