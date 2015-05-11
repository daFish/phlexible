Ext.define('Phlexible.problem.view.MainController', {
    extend: 'Ext.app.ViewController',

    routes: {
        'problems': 'onProblems'
    },

    alias: 'controller.problem.main',

    onProblems: function() {
        alert('onProblems');
    }
});
