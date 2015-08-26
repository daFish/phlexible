Ext.provide('Phlexible.siteroots.TypesGrid');

Ext.require('Phlexible.siteroots.model.Type');

Phlexible.siteroots.TypesGrid = Ext.extend(Ext.grid.GridPanel, {
    title: Phlexible.siteroots.Strings.types.types,
    strings: Phlexible.siteroots.Strings.types,
    border: false,

    initComponent: function () {
        this.viewConfig = {
            emptyText: this.strings.no_types
        };

        this.store = new Ext.data.JsonStore({
            fields: Phlexible.siteroots.model.Type,
            autoLoad: true,
            url: Phlexible.Router.generate('tree_types')
        });

        this.columns = [
            {
                header: this.strings.type,
                dataIndex: 'name',
                sortable: true,
                width: 300
            },
            {
                header: this.strings.allowed,
                dataIndex: 'allowed',
                sortable: true
            },
            {
                header: this.strings.restrictions,
                dataIndex: 'restrictions',
                sortable: true
            }
        ];

        this.sm = new Ext.grid.RowSelectionModel({
            singleSelect: true
        });

        Phlexible.siteroots.TypesGrid.superclass.initComponent.call(this);
    },

    /**
     * After the siteroot selection changes load the siteroot data.
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

Ext.reg('siteroots-types', Phlexible.siteroots.TypesGrid);
