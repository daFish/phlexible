Ext.namespace('Phlexible.task');

Phlexible.task.TaskManager = Ext.create('Phlexible.task.util.TaskManager');
Phlexible.task.StatusIcons = {
    closed: Phlexible.Icon.get('star'),
    finished: Phlexible.Icon.get('tick'),
    open: Phlexible.Icon.get('new-text'),
    rejected: Phlexible.Icon.get('cross'),
    reopened: Phlexible.Icon.get('arrow-repeat')
};

Phlexible.task.TransitionIcons = {
    close: Phlexible.task.StatusIcons.closed,
    finish: Phlexible.task.StatusIcons.finished,
    open: Phlexible.task.StatusIcons.open,
    reject: Phlexible.task.StatusIcons.rejected,
    reopen: Phlexible.task.StatusIcons.reopened
}