/**
 * Portlet model
 */
Ext.define('Phlexible.dashboard.model.Portlet', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'title', type: 'string'},
        {name: 'description', type: 'string'},
        {name: 'xtype', type: 'string'},
        {name: 'hidden', type: 'boolean'},
        {name: 'iconCls', type: 'string'},
        {name: 'data'},
        {name: 'settings'},
        {name: 'configuraton'}
    ]
});