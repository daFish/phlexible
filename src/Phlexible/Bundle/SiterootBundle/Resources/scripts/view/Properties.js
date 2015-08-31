Ext.define('Phlexible.site.view.Properties', {
    extend: 'Ext.grid.PropertyGrid',

    xtype: 'site.properties',

    border: false,
    emptyText: '_emptyText',

    /**
     * After the site selection changes load the site data.
     *
     * @param {Phlexible.site.model.Site} site
     */
    loadData: function (site) {
        this.setSource(site.data.properties);
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
