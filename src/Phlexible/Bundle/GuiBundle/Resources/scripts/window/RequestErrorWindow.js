/**
 * Request error window
 */
Ext.define('Phlexible.gui.RequestErrorWindow', {
    extend: 'Ext.window.Window',
    requires: [
        'Ext.container.Container',
        'Ext.form.*',
        'Ext.window.Window'
    ],

    title: '_error_window',
    cls: 'p-request-error',
    width: 900,
    minWidth: 900,
    height: 650,
    minHeight: 650,
    modal: true,
    resizable: true,
    maximizable: true,
    contrainHeader: true,
    layout: 'border',

    requestUrlText: 'Request URL',
    requestMethodText: 'Request Method',
    timeoutText: 'Timeout',
    msText: 'ms',
    parametersText: 'Parameters',
    stacktraceText: 'Stacktrace',

    initComponent: function() {
        this.title = this.responseStatus + ' ' + this.responseStatusText;
        this.iconCls = Phlexible.Icon.get('lightning');

        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [{
            xtype: 'form',
            region: 'north',
            height: 160,
            border: false,
            layout: 'hbox',
            bodyPadding: 10,
            fieldDefaults: {
                labelAlign: 'top'
            },
            items: [{
                xtype: 'container',
                flex: 1,
                layout: 'anchor',
                border: false,
                margin: '0 10 0 0',
                items: [{
                    xtype: 'textfield',
                    fieldLabel: this.requestUrlText,
                    value: this.requestUrl,
                    anchor: '100%',
                    readOnly: true
                },{
                    xtype: 'textfield',
                    fieldLabel: this.requestMethodText,
                    value: this.requestMethod,
                    anchor: '100%',
                    readOnly: true
                },{
                    xtype: 'textfield',
                    fieldLabel: this.timeoutText,
                    value: this.requestTimeout + ' ' + this.msText,
                    anchor: '100%',
                    readOnly: true
                }]
            },{
                xtype: 'container',
                flex: 1,
                layout: 'anchor',
                border: false,
                items: [{
                    xtype: 'label',
                    text: this.parametersText + ':'
                },{
                    xtype: 'propertygrid',
                    anchor: '100%',
                    source: this.requestParams || {},
                    border: true,
                    height: 120,
                    padding: '5 0 0 0'
                }]
            }]
        },{
            xtype: 'tabpanel',
            region: 'center',
            activeTab: 0,
            plain: true,
            border: false,
            items: this.getExceptionTabs(this.exception)
        }];
    },

    getExceptionTabs: function(exception) {
        var tabs = [],
            exceptions = this.flattenExceptionHierarchy(exception),
            total = exceptions.length,
            i = total;

        Ext.each(exceptions, function(exception) {
            var config = this.getExceptionTabConfig(exception);
            config.title = '[' + i + '/' + total + '] ' + config.title;
            tabs.push(config);
            i -= 1;
        }, this);

        return tabs;
    },

    flattenExceptionHierarchy: function(exception) {
        var exceptions = [],
            previous;

        if (exception.previous) {
            previous = exception.previous;
            delete exception.previous;
        }

        exceptions.push(exception);

        if (previous) {
            Ext.each(this.flattenExceptionHierarchy(previous), function(p) {
                exceptions.push(p);
            }, this);
        }

        return exceptions;
    },

    getExceptionTabConfig: function(exception) {
        return {
            title: exception.classname + ' (' + exception.code + ')',
            layout: 'fit',
            border: false,
            dockedItems: [{
                dock: 'top',
                bodyPadding: 5,
                border: false,
                maxHeight: 150,
                autoScroll: true,
                html: Ext.util.Format.nl2br(exception.message)
            }],
            items: [{
                xtype: 'textarea',
                value: exception.stacktrace,
                disabled: !exception.stacktrace ? true : false,
                border: false,
                readOnly: true,
                anchor: '100%',
                padding: 5
            }]
        }
    }
});