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
            bind: '{problems}',
        },{
            region: 'south',
            layout: 'form',
            padding: '0 5 5 5',
            bodyPadding: 5,
            height: 200,
            items: [{
                xtype: 'textfield',
                anchor: '100%',
                fieldLabel: this.problemText,
                bind: {
                    value: '{list.selection.msg}'
                }
            },{
                xtype: 'textfield',
                anchor: '100%',
                fieldLabel: this.solutionText,
                bind: {
                    value: '{list.selection.hint}'
                }
            },{
                xtype: 'textfield',
                anchor: '100%',
                fieldLabel: this.severityText,
                bind: {
                    value: '{list.selection.severity}'
                }
            },{
                xtype: 'textfield',
                anchor: '100%',
                fieldLabel: this.createdAtText,
                bind: {
                    value: '{list.selection.createdAt}'
                }
            },{
                xtype: 'textfield',
                anchor: '100%',
                fieldLabel: this.lastCheckedAtText,
                bind: {
                    value: '{list.selection.lastCheckedAt}'
                }
            }]
        }];
    }
});
