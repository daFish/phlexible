Ext.provide('Phlexible.tree.view.accordion.Diff');

Phlexible.tree.view.accordion.Diff = Ext.extend(Ext.Panel, {
    title: Phlexible.elements.Strings.differences,
    tabTip: Phlexible.elements.Strings.differences,
    cls: 'p-tree-diff',
    iconCls: 'p-element-diff-icon',
    height: 200,
    autoHeight: true,
    bodyStyle: 'padding: 5px;',

    key: 'comment',

    initComponent: function () {
        this.element.on({
            load: this.onLoadElement,
            diff: function (diff, cb, scope) {
                if (cb && scope) {
                    cb.call(scope, diff, this);
                    this.expand();
                }
                else if (cb) {
                    cb(diff, this);
                    this.expand();
                }
                else {
                    this.empty();
                }
            },
            clearDiff: this.empty,
            scope: this
        });

        this.html = Phlexible.elements.Strings.no_differences;

        Phlexible.tree.view.accordion.Diff.superclass.initComponent.call(this);
    },

    empty: function () {
        this.body.update(Phlexible.elements.Strings.no_differences);
    },

    onLoadElement: function (element) {
        this.empty();

        this.diff = element.getDiff();
    }
});

Ext.reg('tree-accordion-diff', Phlexible.tree.view.accordion.Diff);
