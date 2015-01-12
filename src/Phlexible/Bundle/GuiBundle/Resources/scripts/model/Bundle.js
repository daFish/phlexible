/**
 * Load entry model
 */
Ext.define('Phlexible.gui.model.Bundle', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'classname', type: 'string'},
        {name: 'path', type: 'string'},
        {name: 'package', type: 'string'},
        {name: 'icon', type: 'string'},
    ]
});