Ext.namespace('Phlexible.message');

Phlexible.message.Handlers = [
    ['digest', 'Digest', Phlexible.Icon.get('mail')],
    ['portlet', 'Portlet', Phlexible.Icon.get('application-list')]
];

Phlexible.message.TypeIcons = {
    info: Phlexible.Icon.get('information-white'),
    error: Phlexible.Icon.get('exclamation'),
    0: Phlexible.Icon.get('information-white'),
    1: Phlexible.Icon.get('exclamation')
};

Phlexible.message.TypeNames = {
    0: 'info',
    1: 'error'
};
