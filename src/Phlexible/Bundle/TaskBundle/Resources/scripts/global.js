Ext.namespace('Phlexible.tasks');

Phlexible.tasks.TaskManager = Ext.create('Phlexible.tasks.util.TaskManager');
Phlexible.tasks.StatusIcons = {
    closed: Phlexible.Icon.get('star'),
    finished: Phlexible.Icon.get('tick'),
    open: Phlexible.Icon.get('new-text'),
    rejected: Phlexible.Icon.get('cross'),
    reopened: Phlexible.Icon.get('arrow-repeat')
};

Phlexible.tasks.TransitionIcons = {
    close: Phlexible.tasks.StatusIcons.closed,
    finish: Phlexible.tasks.StatusIcons.finished,
    open: Phlexible.tasks.StatusIcons.open,
    reject: Phlexible.tasks.StatusIcons.rejected,
    reopen: Phlexible.tasks.StatusIcons.reopened
}