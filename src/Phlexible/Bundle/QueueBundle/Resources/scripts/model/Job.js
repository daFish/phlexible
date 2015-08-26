Ext.define('Phlexible.queue.model.Job', {
    extend: 'Ext.data.Model',

    idProperty: 'id',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'command', type: 'string'},
        {name: 'arguments', type: 'string'},
        {name: 'priority', type: 'integer'},
        {name: 'state', type: 'string'},
        {name: 'exitCode', type: 'integer'},
        {name: 'maxRuntime', type: 'integer'},
        {name: 'memoryUsage', type: 'integer'},
        {name: 'runTime', type: 'integer'},
        {name: 'createdAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'startedAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'finishedAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'executeAfter', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'output', type: 'string'},
        {name: 'errorOutput', type: 'string'},
        {name: 'stackTrace', type: 'string'},
        {name: 'fullCommand', calculate: function(data) {
            return data.command + (data.arguments ? ' ' + data.arguments : '');
        }},
        {name: 'memoryUsageFormatted', calculate: function(data) {
            return Phlexible.Format.size(data.memoryUsage);
        }},
        {name: 'createdAtFormatted', calculate: function(data) {
            return Ext.Date.format(data.createdAt, 'Y-m-d H:i:s');
        }},
        {name: 'executeAfterFormatted', calculate: function(data) {
            return Ext.Date.format(data.executeAfter, 'Y-m-d H:i:s');
        }},
        {name: 'startedAtFormatted', calculate: function(data) {
            return Ext.Date.format(data.startedAt, 'Y-m-d H:i:s');
        }},
        {name: 'finishedAtFormatted', calculate: function(data) {
            return Ext.Date.format(data.finishedAt, 'Y-m-d H:i:s');
        }}
    ],
    proxy: {
        type: 'rest',
        url: Phlexible.Router.generate('phlexible_api_queue_get_jobs'),
        simpleSortMode: true,
        reader: {
            type: 'json',
            rootProperty: 'jobs'
        }
    }
});