Ext.define('Phlexible.siteroot.view.PropertyGrid', {
    extend: 'Ext.grid.PropertyGrid',
    alias: 'widget.siteroot-properties',

    title: '_PropertyGrid',
    border: false,
    emptyText: '_emptyText',

    /**
     * After the siteroot selection changes load the siteroot data.
     *
     * @param {Number} id
     * @param {String} title
     * @param {Object} data
     */
    loadData: function (id, title, data) {
        this.setSource(data.properties);
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
