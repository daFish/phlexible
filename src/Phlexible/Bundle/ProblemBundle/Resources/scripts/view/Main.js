Ext.define('Phlexible.problem.view.Main', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Phlexible.problem.model.Problem',
        'Phlexible.problem.view.List',
        'Phlexible.problem.view.MainController'
    ],
    xtype: 'problem.main',
    controller: 'problem.main',

    iconCls: Phlexible.Icon.get('exclamation'),
    layout: 'border',
    border: false,
    referenceHolder: true,
    viewModel: {
        stores: {
            problems: {
                model: 'Phlexible.problem.model.Problem',
                autoLoad: true
            }
        }
    },

    idText: '_idtext',
    problemText: '_problemText',
    severityText: '_severityText',
    sourceText: '_sourceText',
    createdAtText: '_createdAtText',
    lastCheckedAtText: '_lastCheckedAtText',
    linkText: '_linkText',
    solutionText: '_solutionText',

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [{
            xtype: 'problem.list',
            region: 'center',
            reference: 'list',
            padding: 5,
            bind: '{problems}'
        },{
            region: 'south',
            layout: 'form',
            padding: '0 5 5 5',
            bodyPadding: 5,
            height: 160,
            defaults: {
                anchor: '100%',
                readOnly: true
            },
            items: [{
                xtype: 'textfield',
                fieldLabel: this.problemText,
                bind: {
                    value: '{list.selection.message}'
                }
            },{
                xtype: 'textfield',
                fieldLabel: this.solutionText,
                bind: {
                    value: '{list.selection.hint}'
                }
            },{
                xtype: 'displayfield',
                fieldLabel: this.severityText,
                fieldCls: '',
                anchor: null,
                bind: {
                    value: '{list.selection.severity}'
                },
                listeners: {
                    change: function(field, value, oldValue) {
                        field.inputEl.addCls('p-label');
                        field.inputEl.setStyle('display', 'inline');
                        field.inputEl.removeCls('p-label-problem-' + oldValue);
                        field.inputEl.addCls('p-label-problem-' + value);
                    }
                }
            },{
                xtype: 'textfield',
                fieldLabel: this.createdAtText,
                bind: {
                    value: '{list.selection.createdAt}'
                }
            },{
                xtype: 'textfield',
                fieldLabel: this.lastCheckedAtText,
                bind: {
                    value: '{list.selection.lastCheckedAt}'
                }
            }]
        }];
    }
});
