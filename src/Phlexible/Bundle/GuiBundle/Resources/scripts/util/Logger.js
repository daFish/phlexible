Ext.define('Phlexible.gui.model.LogItem', {
    extend: 'Ext.data.Model',

    fields: [
        {name: 'id', type: 'int'},
        {name: 'line', type: 'string'},
        {name: 'severity', type: 'string'},
        {name: 'ts', type: 'date'}
    ]
});

/**
 * Logger
 */
Ext.define('Phlexible.gui.util.Logger', {
    constructor: function() {
        this.logs = Ext.create('Ext.data.Store', {
            model: 'Phlexible.gui.model.LogItem',
            data: [],
            sorters: [{
                property: 'id',
                direction: 'DESC'
            }]
        });
    },

    /**
     * @cfg {Array} log Logs
     */
    logs: [],

    currentId: 1,

    /**
     * Write a debug log line
     *
     * @param {String} line
     */
    debug: function(line) {
        this.addItem(line, 'debug');
        Phlexible.console.debug('Logger:', line);
    },

    /**
     * Write an info log line
     *
     * @param {String} line
     */
    info: function(line) {
        this.addItem(line, 'info');
        Phlexible.console.info('Logger:', line);
    },

    /**
     * Write a notice log line
     *
     * @param {String} line
     */
    notice: function(line) {
        this.addItem(line, 'notice');
        Phlexible.console.info('Logger:', line);
    },

    /**
     * Write a warning error log line
     *
     * @param {String} line
     */
    warn: function(line) {
        this.addItem(line, 'error');
        Phlexible.console.warn('Logger:', line);
    },

    /**
     * Write an error log line
     *
     * @param {String} line
     */
    error: function(line) {
        this.addItem(line, 'error');
        Phlexible.console.error('Logger:', line);
    },

    /**
     * @private
     */
    addItem: function(line, severity) {
        this.logs.add(Ext.create('Phlexible.gui.model.LogItem', {id: this.currentId++, line: line, severity: severity, ts: new Date()}));
    },

    /**
     * Show logger window
     */
    show: function() {
        if (!this.win) {
            this.win = Ext.create('Ext.window.Window', {
                title: 'Logger',
                width: 800,
                height: 500,
                layout: 'fit',
                modal: true,
                maximizable: true,
                closeAction: 'hide',
                items: [{
                    xtype: 'grid',
                    store: this.logs,
                    border: false,
                    columns: [{
                        header: 'ID',
                        dataIndex: 'id',
                        width: 60,
                        hidden: true,
                    },{
                        xtype: 'datecolumn',
                        header: 'Date',
                        dataIndex: 'ts',
                        width: 120,
                        format: 'Y-m-d H:i:s'
                    },{
                        header: 'Severity',
                        dataIndex: 'severity',
                        width: 70
                    },{
                        header: 'Line',
                        dataIndex: 'line',
                        flex: 1
                    }]
                }]
            });
            /*
            this.logs.on('add', function() {
                this.win.getComponent(0).scrollTo(0,0);
            }, this);
            */
        }

        this.win.show();
    }
});

