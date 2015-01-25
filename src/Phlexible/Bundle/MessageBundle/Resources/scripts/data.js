Ext.namespace('Phlexible.message');

Phlexible.message.Handlers = [
    ['digest', 'Digest', Phlexible.Icon.get('mail')],
    ['portlet', 'Portlet', Phlexible.Icon.get('application-list')]
];

Phlexible.message.PriorityIcons = {
    urgent: Phlexible.Icon.get('exclamation-red'),
    high: Phlexible.Icon.get('arrow-090'),
    normal: Phlexible.Icon.get('arrow-000-small'),
    low: Phlexible.Icon.get('arrow-270'),
    3: Phlexible.Icon.get('exclamation-red'),
    2: Phlexible.Icon.get('arrow-090'),
    1: Phlexible.Icon.get('arrow-000-small'),
    0: Phlexible.Icon.get('arrow-270')
};

Phlexible.message.TypeIcons = {
    info: Phlexible.Icon.get('information'),
    error: Phlexible.Icon.get('exclamation'),
    0: Phlexible.Icon.get('information'),
    1: Phlexible.Icon.get('exclamation')
};