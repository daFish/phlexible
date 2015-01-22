Ext.define('Phlexible.problem.handler.Problems', {
    extend: 'Phlexible.gui.menuhandle.handle.XtypeHandle',
    requires: ['Phlexible.problem.view.ProblemsGrid'],

    text: '_problems',
    iconCls: Phlexible.Icon.get('exclamation'),
    name: 'problem-list'
});
