var clientUnique = Math.random().toString(36).substring(4, 11)
var sessionId = '';
var itemCounter = 0;
var skippedItemCounter = 0;
var maxLogItems = 1000;
var pinnedPrefix = 'pinned-';
var frozen = false;
var localStorageKey = 'stdoutSessionId';

function connect() {
    if (sessionId.length === 0) {
        alert('No session id defined. Please refresh the page and try again');
    }
    var wss = new WebSocket($('#wssUrl').val());
    wss.onopen = function () {
        console.log("Connection established! Registering session " + sessionId);
        wss.send('StdoutOnline-Register-Session ' + sessionId);
        $('.connectionSuccess').removeClass('hide');
        $('.connectionPending').addClass('hide');
    };

    wss.onmessage = function (e) {
        if (itemCounter === 0) {
            $('.codeSnippetsWrapper').addClass('hide');
        }
        if (frozen) {
            skippedItemCounter++;
            $('.latestFreezeItem .freezeDiscardedMessages').text(skippedItemCounter);
            return;
        }
        itemCounter++;
        var $logItemToClone = $('div.logItemContainer.toClone');
        $logItemToClone
            .clone()
            .removeClass('toClone hide')
            .addClass('countableItem')
            .insertAfter($logItemToClone)
            .attr('id', 'item-' + itemCounter)
            .find('.logItem')
            .text(JSON.parse(e.data))
            .end()
            .find('.timestamp')
            .html(getDateTimeStr() + '<small>.' + getCurrentMilliseconds() + '</small>')
            .end()
            .find('.logItemCounter')
            .html('<a href="#item-' + itemCounter + '">#' + itemCounter + '</a>')
            .end()
            .find('.logItemCardBody')
            .addClass('newItemHighlightBg')
            .end()
            .hide()
            .fadeIn(100)
        ;

        var $logItems = $('div.logItemContainer.countableItem');
        if ($logItems.length > maxLogItems) {
            $logItems.last().remove();
        }
    };

    wss.onclose = function (e) {
        $('.connectionSuccess').addClass('hide');
        $('.connectionPending').removeClass('hide');
        console.log('Socket is closed. Retrying connection...', e.reason);
        connect();
    };

    wss.onerror = function (e) {
        $('.connectionSuccess').addClass('hide');
        $('.connectionPending').removeClass('hide');
        wss.close();
    };
}

function getDateTimeStr() {
    var now = new Date();
    var year = now.getFullYear();
    var month = now.getMonth() + 1;
    var day = now.getDate();
    var hour = now.getHours();
    var min = now.getMinutes();
    var sec = now.getSeconds();

    month = (month.toString().length === 1) ? ('0' + month) : month;
    day = (day.toString().length === 1) ? ('0' + day) : day;
    hour = (hour.toString().length === 1) ? ('0' + hour) : hour;
    min = (min.toString().length === 1) ? ('0' + min) : min;
    sec = (sec.toString().length === 1) ? ('0' + sec) : sec;

    return year + '/' + month + '/' + day + ' ' + hour + ':' + min + ':' + sec;
}

function getCurrentMilliseconds() {
    var ms = (new Date()).getMilliseconds();
    ms = (ms.toString().length === 1) ? ('0' + ms) : ms;
    ms = (ms.toString().length === 2) ? ('0' + ms) : ms;

    return ms;
}

function pinItem($pinLink) {
    var $container = $pinLink.closest('.logItemContainer');
    togglePinnedItemContainer($container);
    clonePinnedItemContainer($container);
}

function unpinItem($pinLink) {
    $pinLink.removeClass('.pinned');
    var $container = $pinLink.closest('.logItemContainer');
    if ($container.hasClass('pinnedItemCopy')) {
        var id = $container.attr('id').substring(pinnedPrefix.length);
        $container.remove();
        togglePinnedItemContainer($('#' + id));
    } else {
        togglePinnedItemContainer($container);
        $('#' + pinnedPrefix + $container.attr('id')).remove();
    }
}

function togglePinnedItemContainer($container) {
    var $pinLink = $container.find('.logItemPinLink');
    $pinLink.toggleClass('pinned');
    $pinLink.text($pinLink.text() === 'Pin' ? 'Unpin' : 'Pin');
    $container
        .find('.pinAsterisk')
        .toggleClass('hide')
    ;
}

function clonePinnedItemContainer($container) {
    $container
        .clone()
        .removeClass('originalItem')
        .attr('id', pinnedPrefix + $container.attr('id'))
        .addClass('pinnedItemCopy')
        .find('.logItemCardBody ')
        .removeClass('newItemHighlightBg')
        .end()
        .find('.logItemHighlightLink, .logItemRemoveLink')
        .remove()
        .end()
        .appendTo('.pinnedItems')
    ;
}

function freezeLog($freezeLink) {
    $('body').toggleClass('frozen');
    var $small = $freezeLink.find('small');
    $small.text($small.text() === 'Freeze' ? 'Unfreeze' : 'Freeze');
    frozen = !frozen;
    if (frozen) {
        $('div.freezeItem.toClone')
            .clone()
            .removeClass('toClone hide')
            .addClass('latestFreezeItem')
            .insertAfter($('div.logItemContainer.toClone'))
            .find('.timestamp')
            .html(getDateTimeStr() + '<small>.' + getCurrentMilliseconds() + '</small>')
            .end()
            .hide()
            .fadeIn(100)
        ;
    } else {
        $('.latestFreezeItem')
            .removeClass('latestFreezeItem')
            .find('.logItem .freezeText')
            .addClass('strikeThroughText')
            .end()
            .find('.logItem .unfreezeText')
            .html('Unfrozen at ' + getDateTimeStr() + '<small>.' + getCurrentMilliseconds() + '</small>')
            .end()
            .find('.freezeItemLinks')
            .removeClass('hide')
        ;
        skippedItemCounter = 0;
    }
}

function copyElementTextToClipboard(jqueryObject) {
    var range = document.createRange();
    range.selectNode(jqueryObject[0]);
    window.getSelection().removeAllRanges(); // clear current selection
    window.getSelection().addRange(range); // to select text
    document.execCommand("copy");
    window.getSelection().removeAllRanges();// to deselect
}

function startLogging() {
    $('div.welcome').addClass('hide');
    $('div.theLog').removeClass('hide');

    $('.codeToCopy').each(function (i, codeSnippet) {
        var $codeSnippet = $(codeSnippet);
        var text = $codeSnippet.text().replace('{%%sessionId%%}', sessionId);
        $codeSnippet.text(text);
    });

    $('.sessionIdBadge').text(sessionId);

    localStorage.setItem(localStorageKey, sessionId);

    connect();
}

$(function () {
    if (localStorage.getItem(localStorageKey) !== null && localStorage.getItem(localStorageKey).length > 0) {
        $('.lastSessionIdLink')
            .removeClass('hide')
            .find('.lastSessionId')
            .text(localStorage.getItem(localStorageKey))
        ;
    }

    $('#sessionId').val($('#serverUnique').val() + clientUnique);

    $(document).on('click', '.lastSessionIdLink', function (e) {
        $('#sessionId').val(localStorage.getItem(localStorageKey));
        e.preventDefault();
    });

    $(document).on('click', '#startLogging', function (e) {
        sessionId = $('#sessionId').val();
        if (sessionId.length === 0) {
            alert('Session id cannot be empty');
        }
        startLogging();
        e.preventDefault();
    });

    $(document).on('click', '.copyCodeLink', function (e) {
        copyElementTextToClipboard($(this).siblings('pre.codeToCopy'));
        e.preventDefault();
    });

    $(document).on('click', '.logItemPinLink:not(.pinned)', function (e) {
        pinItem($(this));
        e.preventDefault();
    });

    $(document).on('click', '.logItemPinLink.pinned', function (e) {
        unpinItem($(this));
        e.preventDefault();
    });

    $(document).on('click', '.logItemHighlightLink', function (e) {
        $(this)
            .closest('.logItemContainer')
            .toggleClass('highlightItem')
            .find('.logItemCardHeader')
            .toggleClass('highlightItem');
        e.preventDefault();
    });

    $(document).on('click', '.logItemCollapseLink', function (e) {
        var $this = $(this);
        $this.text($this.text() === 'Collapse' ? 'Uncollapse' : 'Collapse');
        $this
            .closest('.logItemContainer')
            .find('.logItem, .card-body')
            .toggleClass('collapsed')
        ;
        e.preventDefault();
    });

    $(document).on('click', '.logItemRemoveLink', function (e) {
        $(this).closest('.removableItem').fadeOut(250, function () {
            $(this).remove();
        });
        e.preventDefault();
    });

    $(document).on('click', '.getCodeLink', function (e) {
        $('.codeSnippetsWrapper').removeClass('hide');
        e.preventDefault();
    });

    $(document).on('click', '.codeSnippetsCloseLink', function (e) {
        $('.codeSnippetsWrapper').addClass('hide');
        e.preventDefault();
    });

    $(document).on('click', '.freezeLogLink', function (e) {
        freezeLog($(this));
        e.preventDefault();
    });
});