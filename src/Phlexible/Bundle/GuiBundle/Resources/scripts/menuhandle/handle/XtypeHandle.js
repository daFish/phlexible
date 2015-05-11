Ext.define('Phlexible.gui.menuhandle.handle.XtypeHandle', {
    extend: 'Phlexible.gui.menuhandle.handle.Handle',

    /**
     * @cfg {String} name
     */

    /**
     * @cfg {Object} parameters
     */

    /**
     * Return name
     *
     * @return {String}
     */
    getName: function () {
        return this.name;
    },

    getIdentifier: function () {
        return this.getName().replace(/\./g, '_');
    },

    getParameters: function () {
        return this.parameters || {};
    },

    setParameters: function (parameters) {
        this.parameters = parameters;
    },

    handle: function () {
        Phlexible.Logger.debug('XtypeHandle.handle(' + this.getName() + ', ' + this.getIdentifier() + ')', this.getParameters());

        Phlexible.App.addPanelByHandle(this);
    }
});