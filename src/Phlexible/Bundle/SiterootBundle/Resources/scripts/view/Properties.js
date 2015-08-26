Ext.define('Phlexible.siteroot.view.Properties', {
    extend: 'Ext.grid.PropertyGrid',

    xtype: 'siteroot.properties',

    border: false,
    emptyText: '_emptyText',

    /**
     * After the siteroot selection changes load the siteroot data.
     *
     * @param {Phlexible.siteroot.model.Siteroot} siteroot
     */
    loadData: function (siteroot) {
        this.setSource(siteroot.data.properties);
    },

    isValid: function () {
        return true;
    },

    /**
     * Get the data to be saved.
     */
    getSaveData: function () {
        var values = this.getSource();

        return {
            properties: values
        };
    }
});
