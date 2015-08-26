/*jsl:ignoreall*/

/**
 * disable backspace
 *
 * @see http://stackoverflow.com/questions/3850442/how-to-prevent-browsers-default-history-back-action-for-backspace-button-with-ja
 */
function suppressBackspace(evt) {
    evt = evt || window.event;
    var target = evt.target || evt.srcElement;

    if (evt.keyCode == 8 &&
        !/input|textarea/i.test(target.nodeName) &&
        (target.getAttribute("class") &&
        target.getAttribute("class") !== "redactor_ redactor_editor")) {
        return false;
    }
}

document.onkeydown = suppressBackspace;
document.onkeypress = suppressBackspace;

/* Set default ExtJs Ajax Timeout to 2 minutes. */
Ext.Ajax.timeout = 2 * 60 * 1000;
