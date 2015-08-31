Ext.define('Phlexible.site.view.NodeConstraints', {
    extend: 'Ext.grid.Panel',
    xtype: 'site.node-constraints',

    border: false,
    emptyText: '_emptyText',

    typeText: '_typeText',
    allowedText: '_allowedText',
    restrictionsText: '_restrictionsText',

    initComponent: function () {
        this.initMyColumns();

        this.callParent(arguments);
    },

    initMyColumns: function() {
        this.columns = [
            {
                header: this.typeText,
                dataIndex: 'name',
                sortable: true,
                width: 300
            },
            {
                header: this.allowedText,
                dataIndex: 'allowed',
                sortable: true
            },
            {
                header: this.restrictionsText,
                dataIndex: 'restrictions',
                sortable: true
            }
        ];
    },

    /**
     * After the site selection changes load the site data.
     *
     * @param {Number} id
     * @param {String} title
     * @param {Object} data
     */
    loadData: function (id, title, data) {
        console.info(data);
        this.store.each(function(r) {
            if (!data.nodeConstraints[r.get('name')]) {
                r.set('allowed', false);
            } else if (data.nodeConstraints[r.get('name')].allowed) {
                r.set('allowed', true);
            } else {
                r.set('allowed', false);
            }
        });
    },

    isValid: function () {
        var valid = true;

        return valid;
    },

    /**
     * Get the data to be saved.
     */
    getSaveData: function () {
        // check data
        var valid = true;
        Ext.each(this.store.getModifiedRecords() || [], function(r) {
        });

        if (!valid) {
            return false;
        }

        // fetch modified records
        var modified = [];
        Ext.each(this.store.getModifiedRecords() || [], function (r) {
            modified.push(r.data);
        });

        return {
            types: modified
        };
    }

});
