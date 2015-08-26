/**
 * Portlet model
 */
Ext.define('Phlexible.dashboard.model.Portlet', {
    extend: 'Ext.data.Model',
    idProperty: 'id',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'image', type: 'string'},
        {name: 'xtype', type: 'string'},
        {name: 'hidden', type: 'boolean'},
        {name: 'iconCls', type: 'string'},
        {name: 'role', type: 'string'},
        {name: 'data'},
        {name: 'settings'},
        {name: 'configuraton'},
        {name: 'title', type: 'string', calculate: function(data) {
            var cls = Ext.ClassManager.getByAlias('widget.' + data.xtype);
            if (!cls) {
                Phlexible.console.warn('Portlet widget.' + data.xtype + ' not found.');
                return;
            }
            return cls.prototype.config.title || cls.prototype.title || data.xtype;
        }},
        {name: 'description', type: 'string', calculate: function(data) {
            var cls = Ext.ClassManager.getByAlias('widget.' + data.xtype);
            if (!cls) {
                Phlexible.console.warn('Portlet widget.' + data.xtype + ' not found.');
                return;
            }
            return cls.prototype.config.description || cls.prototype.description || '';
        }}
    ]
});
