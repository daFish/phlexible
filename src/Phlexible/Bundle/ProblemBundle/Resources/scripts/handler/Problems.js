Ext.define('Phlexible.problem.handler.Problems', {
    extend: 'Phlexible.gui.menuhandle.handle.XtypeHandle',
    requires: ['Phlexible.problem.view.Problems'],

    text: '_problems',
    iconCls: Phlexible.Icon.get('exclamation'),
    name: 'problem.problems'
});
