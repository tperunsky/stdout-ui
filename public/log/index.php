<?php
$sessionId = $_GET['id'] ?? null;
if (empty($sessionId)) {
    die('No session id provided in url. Add ?id=... to the url.');
}
$config = require __DIR__ . '/../../config/config.php';
$host = $config['websocketServer']['host'];
$port = $config['websocketServer']['port'];
$wsUrl = $host . ':' . $port;


?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>stdout.online</title>

    <style>
        body {
            background-color: #000000;
            color: whitesmoke;
        }

        a {
            outline: none;
        }

        hr {
            border-top: 1px solid rgba(255,255,255,.2);
        }

        pre.card-text {
            color: #ffffff;
        }

        .hide {
            display: none !important;
        }

        .logItemLinks {
            padding-right: 10px;
        }

        .logItemLinks > * {
            padding-right: 15px;
        }

        .logItemLinks a.logItemPinLink {
            color:
        }

        .card-header.logItemCardHeader {
            border-bottom: 1px solid rgb(52, 52, 52)
        }

        .logItemContainer.originalItem.highlightItem {
            background-color: #d06868;
        }

        .logItemContainer.originalItem .card .logItemCardHeader.highlightItem {
            background-color: #d06868;
        }

        .logItemCardHeader.card-header {
            padding: .15rem 1.25rem 0.15rem 0.5rem;
            background-color: #3d3d3d;
        }

        .logItemCardBody.card-body.collapsed {
            padding: 0.25rem;
        }

        .logItem.collapsed {
            display: none;
        }

        .pinAsteriskWrapper {
            min-width: 0.45rem;
            display: inline-block;
        }

        .pinnedItemCopy .logItemCardHeader {
            background-color: rgba(81, 33, 33, 0.4);
        }

        .pinnedItemCopy .logItemCardBody {
            background-color: rgba(29, 12, 12, 0.82);
        }

        .freezeItem .logItemCardHeader {
            background-color: #2e69a6;
        }

        .freezeItem .logItemCardBody {
            background-color: #214264;
        }

        .logItemCardBody {
            background-color: #111111;
        }

        @-webkit-keyframes newItemHighlight {
            0% {
                background-color: #2d2d2d;
            }
            100% {
                background-color: #111111;
            }
        }

        .newItemHighlightBg{
          -webkit-animation-name: newItemHighlight;
            -webkit-animation-duration: 15000ms;
            -webkit-animation-iteration-count: 1;
            -webkit-animation-timing-function: linear;
          -moz-animation-name: newItemHighlight;
            -moz-animation-duration: 15000ms;
            -moz-animation-iteration-count: 1;
            -moz-animation-timing-function: linear;
        }

        .freezeLogLink, .getCodeLink{
            padding: 1rem;
        }

        body.frozen {
            background-color: #06062f;
        }

        h4.headerFlex {
            display: flex;
            justify-content: space-between;
        }

        .strikeThroughText {
            text-decoration: line-through;
        }

        .codeSnippetsWrapper {
            margin-bottom: 1rem;
        }

        .codeSnippetsWrapper .card {
            background: #2d2d2d;
            color: whitesmoke;
        }

        .codeSnippetsWrapper .card pre {
            margin-bottom: 0rem;
            color: whitesmoke;
        }

        .codeSnippetsWrapper .codeSnippetsExplanation {
            display: inline-block;
        }

    </style>

  </head>
  <body>

    <header class="container-fluid">
        <div class="page-header">
            <h4 class="headerFlex">
                <span>
                    stdout.online
                    <span class="badge badge-success hide connectionSuccess">Connected</span>
                    <span class="badge badge-warning connectionPending">Connecting...</span>
                </span>
                <span>
                    <a href="#" class="getCodeLink"><small>Get code</small></a>
                    <a href="#" class="freezeLogLink"><small>Freeze</small></a>
                </span>

            </h4>
        </div>
    </header>

    <div class="pinnedItems"></div>

    <hr/>

    <div class="container-fluid codeSnippetsWrapper">
        <div class="card">
            <div class="card-header">
                <p class="codeSnippetsExplanation">Choose your programming language and use the provided code snippet to send log messages to this window.</p>
                <a href="#" class="codeSnippetsCloseLink float-right">Close</a>
                <ul class="nav nav-pills" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-php" role="tab"
                           aria-controls="pills-home" aria-selected="true">PHP</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-ruby" role="tab"
                           aria-controls="pills-profile" aria-selected="false">Ruby</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-java" role="tab"
                           aria-controls="pills-contact" aria-selected="false">Java</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-python" role="tab"
                           aria-controls="pills-contact" aria-selected="false">Python</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-php" role="tabpanel"
                         aria-labelledby="pills-home-tab">
                    <pre>
(function($m){fwrite($c=stream_socket_client('tcp://stdout.online:10660'),json_encode(['m'=>$m,'s'=>'<?= $sessionId ?>']));fclose($c);})
('Your text goes here');</pre>
                    </div>
                    <div class="tab-pane fade" id="pills-ruby" role="tabpanel" aria-labelledby="pills-profile-tab">
                        <pre>not supported yet</pre>
                    </div>
                    <div class="tab-pane fade" id="pills-java" role="tabpanel" aria-labelledby="pills-contact-tab">
                        <pre>not supported yet</pre>
                    </div>
                    <div class="tab-pane fade" id="pills-python" role="tabpanel" aria-labelledby="pills-contact-tab">
                        <pre>not supported yet</pre>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <div class="container-fluid logItemContainer removableItem originalItem toClone hide">
        <div class="card text-white bg-dark mb-3">
          <div class="logItemCardHeader card-header">
              <div class="float-left timestampWrapper">
                  <div class="pinAsteriskWrapper"><span class="pinAsterisk hide">*</span></div>
                  <span class="timestamp"></span>
              </div>
              <div class="float-right logItemCounter"></div>
              <div class="float-right logItemLinks">
                  <a href="#" class="logItemPinLink">Pin</a>
                  <a href="#" class="logItemHighlightLink">Highlight</a>
                  <a href="#" class="logItemCollapseLink">Collapse</a>
                  <a href="#" class="logItemRemoveLink">Remove</a>
              </div>
          </div>
          <div class="logItemCardBody card-body">
            <pre class="logItem card-text"></pre>
          </div>
        </div>
    </div>

    <div class="container-fluid freezeItem removableItem toClone hide">
        <div class="card text-white bg-dark mb-3">
          <div class="logItemCardHeader card-header">
              <div class="float-left timestampWrapper">
                  <div class="pinAsteriskWrapper"></div>
                  <span class="timestamp"></span>
              </div>
              <div class="float-right freezeItemLinks hide">
                  <a href="#" class="logItemRemoveLink">Remove</a>
              </div>
          </div>
          <div class="logItemCardBody card-body">
            <pre class="logItem card-text"><span class="freezeText">Log frozen. All new messages are ignored.</span> Discarded messages: <span class="freezeDiscardedMessages">0</span>. <span class="unfreezeText"></span></pre>
          </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script>


         var itemCounter = 0;
         var skippedItemCounter = 0;
         var maxLogItems = 1000;
         var pinnedPrefix = 'pinned-';
         var frozen = false;

         function connect() {
             var wss = new WebSocket('wss://<?php echo $wsUrl ?>');
             wss.onopen = function () {
                 console.log("Connection established! Registering session <?php echo $sessionId ?>");
                 wss.send('StdoutOnline-Register-Session <?php echo $sessionId ?>');
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

         connect();

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

         $(function(){
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

             $(document).on('click', '.getCodeLink', function(e) {
                 $('.codeSnippetsWrapper').removeClass('hide');
                 e.preventDefault();
             });

             $(document).on('click', '.codeSnippetsCloseLink', function(e) {
                 $('.codeSnippetsWrapper').addClass('hide');
                 e.preventDefault();
             });

             $(document).on('click', '.freezeLogLink', function(e) {

                 freezeLog($(this));
                 e.preventDefault();
             });
         });
    </script>
  </body>
</html>