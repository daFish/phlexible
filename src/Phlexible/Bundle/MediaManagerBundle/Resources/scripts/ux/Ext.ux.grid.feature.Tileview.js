/**
 * @class Ext.ux.grid.feature.Tileview
 * @extends Ext.grid.feature.Feature
 *
 * @author Harald Hanek (c) 2011-2012
 * @license http://harrydeluxe.mit-license.org
 */
Ext.define('Ext.ux.grid.feature.Tileview', {
    extend: 'Ext.grid.feature.Feature',
    alias: 'feature.tileview',

    metaTableTplOrig: null, // stores the original template
    viewMode: null,
    tpls: {},
    rowTpls: {},
    cellTpls: {},

    init: function(grid) {
        var me = this,
            view = me.view;

        me.metaTableTplOrig = me.view.tableTpl;
        //grid.view.tableTpl.html = grid.view.tableTpl.html.replace(/ class=\"(.*?)\" border/, ' class="$1 tileview" border');
        //grid.view.tableTpl.html = grid.view.tableTpl.html.replace(/\{\[view.renderColumnSizer\(out\)\]\}/, '');

        view.tileViewFeature = me;

        Ext.Object.each(this.tpls, function(key, tpl) {
            view.addTpl(new Ext.XTemplate(tpl));
        });

        Ext.Object.each(this.rowTpls, function(key, rowTpl) {
            view.addRowTpl(new Ext.XTemplate(rowTpl));
        });

        Ext.Object.each(this.cellTpls, function(key, cellTpl) {
            view.addCellTpl(new Ext.XTemplate(cellTpl));
        });

        me.callParent(arguments);
    },

    getColumnValues: function(columns, record) {
        var columnValues = {};
        Ext.each(columns, function(column) {
            columnValues[column.dataIndex] = record.data[column.dataIndex];
        });
        return columnValues;
    },

    getRowBody: function(values, viewMode) {
        if(this.rowTpls[viewMode]) {
            return this.rowTpls[viewMode];
        }
    },

    getViewMode: function() {
        return this.viewMode;
    },

    setViewMode: function(view) {
        var me = this;

        if (me.viewMode != view) {
            me.viewMode = view;
            me.view.refresh();
        }
    }
});