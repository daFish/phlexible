Ext.define('Phlexible.problem.view.ProblemsController', {
    extend: 'Ext.app.ViewController',

    routes: {
        'problems': 'onProblems'
    },

    alias: 'controller.problem.problems',

    onProblems: function() {
        alert('onProblems');
    }
});
