Ext.require('Phlexible.gui.util.Application');

Phlexible.gui.util.Application.prototype.initPanels =
    Ext.Function.createSequence(Phlexible.gui.util.Application.prototype.initPanels, function() {
        this.initialPanels.push({
            xtype: 'dashboard-dashboard',
            header: false
        });
    });
