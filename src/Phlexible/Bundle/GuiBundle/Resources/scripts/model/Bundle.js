/**
 * Load entry model
 */
Ext.define('Phlexible.gui.model.Bundle', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'name', type: 'string'},
        {name: 'classname', type: 'string'},
        {name: 'package', type: 'string'},
        {name: 'path', type: 'string'}
    ]
});