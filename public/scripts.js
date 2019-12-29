function StdoutOnline() {
    let parent = this;
    let clientUnique = Math.random().toString(36).substring(4, 11);
    let sessionId = '';
    let itemCounter = 0;
    let skippedItemCounter = 0;
    let maxLogItems = 1000;
    let pinnedPrefix = 'pinned-';
    let frozen = false;
    let localStorageKey = 'stdoutSessionId';
    let stdoutOnlineRegisterSessionPrefix = 'StdoutOnline-Register-Session';

    this.connect = function () {
        if (sessionId.length === 0) {
            alert('No session id defined. Please refresh the page and try again');
        }
        let wss = new WebSocket($('#wssUrl').val());
        parent.attachWebSocketHandlers(wss);
    };

    this.connectionStatusSuccess = function () {
        $('.connectionSuccess').removeClass('hide');
        $('.connectionPending').addClass('hide');
    };

    this.connectionStatusPending = function () {
        $('.connectionSuccess').removeClass('hide');
        $('.connectionPending').addClass('hide');
    };

    this.truncateLog = function () {
        let $logItems = $('div.logItemContainer.countableItem');
        if ($logItems.length > maxLogItems) {

            $logItems.last().remove();
        }
    };

    this.attachWebSocketHandlers = function (wss) {
        wss.onopen = function () {
            console.log("Connection established! Registering session " + sessionId);
            wss.send(stdoutOnlineRegisterSessionPrefix + ' ' + sessionId);
            parent.connectionStatusSuccess();
        };

        wss.onmessage = function (e) {
            if (itemCounter === 0) {
                //hide code snippets when first message arrives
                $('.codeSnippetsWrapper').addClass('hide');
            }
            if (frozen) {
                skippedItemCounter++;
                $('.latestFreezeItem .freezeDiscardedMessages').text(skippedItemCounter);
                return;
            }
            itemCounter++;
            let $logItemToClone = $('div.logItemContainer.toClone');
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
                .html(parent.getDateTimeStr() + '<small>.' + parent.getCurrentMilliseconds() + '</small>')
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

            parent.truncateLog();
        };

        wss.onclose = function (e) {
            parent.connectionStatusPending();
            console.log('Socket is closed. Retrying connection...', e.reason);
            parent.connect();
        };

        wss.onerror = function () {
            parent.connectionStatusPending();
            wss.close();
        };
    };

    this.getDateTimeStr = function () {
        let now = new Date();
        let year = now.getFullYear();
        let month = now.getMonth() + 1;
        let day = now.getDate();
        let hour = now.getHours();
        let min = now.getMinutes();
        let sec = now.getSeconds();

        month = (month.toString().length === 1) ? ('0' + month) : month;
        day = (day.toString().length === 1) ? ('0' + day) : day;
        hour = (hour.toString().length === 1) ? ('0' + hour) : hour;
        min = (min.toString().length === 1) ? ('0' + min) : min;
        sec = (sec.toString().length === 1) ? ('0' + sec) : sec;

        return year + '/' + month + '/' + day + ' ' + hour + ':' + min + ':' + sec;
    };

    this.getCurrentMilliseconds = function () {
        let ms = (new Date()).getMilliseconds();
        ms = (ms.toString().length === 1) ? ('0' + ms) : ms;
        ms = (ms.toString().length === 2) ? ('0' + ms) : ms;

        return ms;
    };

    this.pinItem = function ($pinLink) {
        let $container = $pinLink.closest('.logItemContainer');
        parent.togglePinnedItemContainer($container);
        parent.clonePinnedItemContainer($container);
    };

    this.unpinItem = function ($pinLink) {
        $pinLink.removeClass('.pinned');
        let $container = $pinLink.closest('.logItemContainer');
        if ($container.hasClass('pinnedItemCopy')) {
            let id = $container.attr('id').substring(pinnedPrefix.length);
            $container.remove();
            parent.togglePinnedItemContainer($('#' + id));
        } else {
            parent.togglePinnedItemContainer($container);
            $('#' + pinnedPrefix + $container.attr('id')).remove();
        }
    };

    this.togglePinnedItemContainer = function ($container) {
        let $pinLink = $container.find('.logItemPinLink');
        $pinLink.toggleClass('pinned');
        $pinLink.text($pinLink.text() === 'Pin' ? 'Unpin' : 'Pin');
        $container
            .find('.pinAsterisk')
            .toggleClass('hide')
        ;
    };

    this.clonePinnedItemContainer = function ($container) {
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
    };

    this.freezeUnfreezeLog = function ($freezeLink) {
        $('body').toggleClass('frozen');
        let $small = $freezeLink.find('small');
        $small.text($small.text() === 'Freeze' ? 'Unfreeze' : 'Freeze');
        frozen = !frozen;
        if (frozen) {
            $('div.freezeItem.toClone')
                .clone()
                .removeClass('toClone hide')
                .addClass('latestFreezeItem')
                .insertAfter($('div.logItemContainer.toClone'))
                .find('.timestamp')
                .html(parent.getDateTimeStr() + '<small>.' + parent.getCurrentMilliseconds() + '</small>')
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
                .html('Unfrozen at ' + parent.getDateTimeStr() + '<small>.' + parent.getCurrentMilliseconds() + '</small>')
                .end()
                .find('.freezeItemLinks')
                .removeClass('hide')
            ;
            skippedItemCounter = 0;
        }
    };

    this.copyElementTextToClipboard = function (jqueryObject) {
        let range = document.createRange();
        range.selectNode(jqueryObject[0]);
        window.getSelection().removeAllRanges(); // clear current selection
        window.getSelection().addRange(range); // to select text
        document.execCommand("copy");
        window.getSelection().removeAllRanges();// to deselect
    };

    this.startLogging = function () {
        $('div.welcome').addClass('hide');
        $('div.theLog').removeClass('hide');

        $('.codeToCopy').each(function (i, codeSnippet) {
            let $codeSnippet = $(codeSnippet);
            let text = $codeSnippet.text().replace('{%%sessionId%%}', sessionId);
            $codeSnippet.text(text);
        });

        $('.sessionIdBadge').text(sessionId);

        localStorage.setItem(localStorageKey, sessionId);

        parent.connect();
    };

    this.attachEventListeners = function () {
        //Copy last session id from local storage into the session input
        $(document).on('click', '.lastSessionIdLink', function (e) {
            $('#sessionId').val(localStorage.getItem(localStorageKey));
            e.preventDefault();
        });

        //Hide homepage content and start logging
        $(document).on('click', '#startLogging', function (e) {
            sessionId = $('#sessionId').val();
            if (sessionId.length === 0) {
                alert('Session id cannot be empty');
            }
            parent.startLogging();
            e.preventDefault();
        });

        //Copy code snippet into clipboard
        $(document).on('click', '.copyCodeLink', function (e) {
            parent.copyElementTextToClipboard($(this).siblings('pre.codeToCopy'));
            $('span.copyCodeSuccess').stop().fadeIn(50).delay(200).fadeOut(1500);
            e.preventDefault();
        });

        //Pin log item
        $(document).on('click', '.logItemPinLink:not(.pinned)', function (e) {
            parent.pinItem($(this));
            e.preventDefault();
        });

        //Unpin pinned log item
        $(document).on('click', '.logItemPinLink.pinned', function (e) {
            parent.unpinItem($(this));
            e.preventDefault();
        });

        //Highlight log item
        $(document).on('click', '.logItemHighlightLink', function (e) {
            $(this)
                .closest('.logItemContainer')
                .toggleClass('highlightItem')
                .find('.logItemCardHeader')
                .toggleClass('highlightItem');
            e.preventDefault();
        });

        //Collapse log item
        $(document).on('click', '.logItemCollapseLink', function (e) {
            let $this = $(this);
            $this.text($this.text() === 'Collapse' ? 'Uncollapse' : 'Collapse');
            $this
                .closest('.logItemContainer')
                .find('.logItem, .card-body')
                .toggleClass('collapsed')
            ;
            e.preventDefault();
        });

        //Remove log item
        $(document).on('click', '.logItemRemoveLink', function (e) {
            $(this).closest('.removableItem').fadeOut(250, function () {
                $(this).remove();
            });
            e.preventDefault();
        });

        //Show code snippets
        $(document).on('click', '.getCodeLink', function (e) {
            $('.codeSnippetsWrapper').removeClass('hide');
            e.preventDefault();
        });

        //Hide code snippets
        $(document).on('click', '.codeSnippetsCloseLink', function (e) {
            $('.codeSnippetsWrapper').addClass('hide');
            e.preventDefault();
        });

        //Freeze/unfreeze log
        $(document).on('click', '.freezeLogLink', function (e) {
            parent.freezeUnfreezeLog($(this));
            e.preventDefault();
        });
    };

    this.displayLastSessionId = function () {
        //If there's a session id stored in local storage, display link to use last session id below session input
        if (localStorage.getItem(localStorageKey) !== null && localStorage.getItem(localStorageKey).length > 0) {
            $('.lastSessionIdLink')
                .removeClass('hide')
                .find('.lastSessionId')
                .text(localStorage.getItem(localStorageKey))
            ;
        }
    };

    this.generateSessionId = function () {
        $('#sessionId').val($('#serverUnique').val() + clientUnique);
    };

    this.javascriptLoaded = function() {
        $('.hideOnJs').addClass('hide');
    };

    this.init = function () {
        parent.javascriptLoaded();
        parent.displayLastSessionId();
        parent.generateSessionId();
        parent.attachEventListeners();
    };
}

let stdoutOnline = new StdoutOnline();
$(stdoutOnline.init);