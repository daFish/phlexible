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

    title: 'Request error',
    cls: 'p-request-error',
    width: 960,
    minWidth: 960,
    height: 700,
    minHeight: 700,
    modal: true,
    resizable: true,
    maximizable: true,
    contrainHeader: true,
    plain: true,
    layout: 'border',

    urlText: 'Request URL',
    methodText: 'Method',
    timeoutText: 'Timeout',
    msText: 'ms',
    requestHeadersText: 'Request Headers',
    responseHeadersText: 'Response Headers',
    parametersText: 'Request data',
    stacktraceText: 'Stacktrace',
    payloadText: 'Payload',

    initComponent: function() {
        this.title = this.response.status + ' ' + this.response.statusText;
        this.iconCls = Phlexible.Icon.get('lightning');

        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [{
            xtype: 'form',
            region: 'north',
            height: 235,
            border: false,
            bodyPadding: 5,
            fieldDefaults: {
                labelAlign: 'top'
            },
            items: [{
                xtype: 'container',
                layout: 'hbox',
                items: [{
                    xtype: 'textfield',
                    fieldLabel: this.urlText,
                    value: this.request.url,
                    flex: 7,
                    readOnly: true
                },{
                    xtype: 'textfield',
                    fieldLabel: this.methodText,
                    value: this.request.method,
                    flex: 1,
                    margin: '0 0 0 5',
                    readOnly: true
                },{
                    xtype: 'textfield',
                    fieldLabel: this.timeoutText,
                    value: this.request.timeout + ' ' + this.msText,
                    flex: 1,
                    margin: '0 0 0 5',
                    readOnly: true
                }]
            },{
                xtype: 'container',
                layout: 'hbox',
                items: [{
                    xtype: 'container',
                    flex: 1,
                    layout: 'anchor',
                    border: false,
                    items: [{
                        xtype: 'textarea',
                        fieldLabel: this.requestHeadersText,
                        anchor: '100%',
                        value: JSON.stringify(this.request.headers || {}, null, '  '),
                        border: true,
                        height: 180
                    }]
                },{
                    xtype: 'container',
                    flex: 1,
                    layout: 'anchor',
                    border: false,
                    margin: '0 0 0 5',
                    items: [{
                        xtype: 'textarea',
                        fieldLabel: this.parametersText,
                        anchor: '100%',
                        value: JSON.stringify(this.request.params || this.request.jsonData, null, '  '),
                        border: true,
                        height: 180
                    }]
                },{
                    xtype: 'container',
                    flex: 1,
                    layout: 'anchor',
                    border: false,
                    margin: '0 0 0 5',
                    items: [{
                        xtype: 'textarea',
                        fieldLabel: this.responseHeadersText,
                        anchor: '100%',
                        value: JSON.stringify(this.response.headers || {}, null, '  '),
                        border: true,
                        height: 180
                    }]
                }]
            }]
        },
            this.createErrorPanel()
        ];
    },

    createErrorPanel: function() {
        var config = {
            xtype: 'container',
            region: 'center',
            layout: 'accordion',
            border: false
        };

        if (this.response.payload.trace) {
            config.items = this.createExceptionTabs(this.response.payload);
        } else {
            config.items = {
                xtype: 'panel',
                title: this.payloadText,
                iconCls: Phlexible.Icon.get('box'),
                bodyPadding: ' 0 10 10 10',
                html: '<pre>' + JSON.stringify(this.response.payload, null, '  ') + '</pre>'
            };
        }

        return config;
    },

    createExceptionTabs: function(exception) {
        var tabs = [],
            exceptions = this.flattenExceptionHierarchy(exception),
            total = exceptions.length,
            i = total;

        Ext.each(exceptions, function(exception) {
            var config = this.createExceptionTabConfig(exception);
            config.title = (i > 1 ? '[' + i + '/' + total + '] ' : '') + config.title;
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

    createExceptionTabConfig: function(exception) {
        var config = {
            title: exception.classname + ' (' + exception.code + ')',
            iconCls: Phlexible.Icon.get('fire'),
            border: false,
            autoScroll: true,
            bodyPadding: 15,
            items: [{
                xtype: 'container',
                html: '<b>' + Ext.util.Format.nl2br(exception.message) + '</b>'
            }]
        };

        if (exception.excerpt) {
            config.items.push({
                xtype: 'container',
                html: exception.excerpt
            });
        }

        var traces = [];
        Ext.each(exception.trace, function(trace) {
            var excerpt = '';
            if (trace.excerpt) {
                excerpt = '<div style="padding: 5px; border: 1px solid gray; margin: 5px 0 15px 0;">' + trace.excerpt + '</div>';
            }
            traces.push(
                '<li style="padding-top: 5px;">'+
                    (trace.class || trace.function ? 'at <b>' + (trace.class ? trace.class : ' ') + (trace.type ? trace.type : ' ') + (trace.function ? trace.function : ' ') + '</b>' + (trace.args ? ' (' + JSON.stringify(trace.args) + ')': ' ') + '<br/>' : '') +
                    (trace.file && trace.line ? 'in ' + (trace.file) + ' at line ' + trace.line : '') +
                    excerpt +
                '</li>'
            );
        });
        config.items.push({
            xtype: 'container',
            html: '<ol>' + traces.join('') + '</ol>'
        });

        return config;
    }
});