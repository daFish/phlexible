Ext.define('Phlexible.search.model.Result', {
    extend: 'Ext.data.Model',
    fields: [
        'id',
        'image',
        'title',
        'author',
        {name: 'date', type: 'date', dateFormat: 'timestamp'},
        'component',
        'menu'
    ]
});
