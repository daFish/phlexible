Ext.define('Phlexible.problem.view.ProblemsController', {
    extend: 'Ext.app.ViewController',

    routes: {
        problems: 'onProblems'
    },

    alias: 'controller.problem.problems',

    onProblems: function() {
        console.log(arguments);

        var handler = Phlexible.Handlers.get('problems');
        console.log(handler);
    }
});
