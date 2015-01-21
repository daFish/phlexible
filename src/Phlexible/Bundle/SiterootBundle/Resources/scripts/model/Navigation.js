Ext.define('Phlexible.siteroot.model.Navigation', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id'},
        {name: 'title'},
        {name: 'handler'},
        {name: 'start_tid'},
        {name: 'max_depth'},
        {name: 'flags'},
        {name: 'supports'},
        {name: 'additional'},
        {name: 'hide_config', type: 'bool'}
    ]
});
