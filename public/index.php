<?php
$sessionId = $_GET['id'] ?? null;
if (empty($sessionId)) {
    die('No session id provided in url. Add ?id=... to the url.');
}
$config = require __DIR__ . '/../config/config.php';
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
        pre.card-text {
            color: #ffffff;
        }

        .hide {
            display: none !important;
        }

        .logItemHeader {
            cursor: pointer;
        }

        .card-header.logItemCardHeader {
            border-bottom: 1px solid rgb(52, 52, 52)
        }

        @-webkit-keyframes highlight {
            0% {
                background-color: #5d5d5d;
            }
            1% {
                background-color: black;
            }
        }

        .highlightBg{
          -webkit-animation-name: highlight;
            -webkit-animation-duration: 15000ms;
            -webkit-animation-iteration-count: 1;
            -webkit-animation-timing-function: linear;
          -moz-animation-name: highlight;
            -moz-animation-duration: 15000ms;
            -moz-animation-iteration-count: 1;
            -moz-animation-timing-function: linear;
        }

    </style>

  </head>
  <body>
    <div class="container-fluid">
        <div class="page-header">
            <h3>
                << stdout.online
                <span class="badge badge-success hide connectionSuccess">Connected</span>
                <span class="badge badge-warning connectionPending">Connecting...</span>
            </h3>
        </div>
    </div>

    <div class="container-fluid logItemContainer toClone hide">
        <div class="card text-white bg-dark mb-3">
          <div class="logItemCardHeader card-header logItemHeader">
              <div class="float-left timestamp"></div>
              <div class="float-right logItemCounter"></div>
          </div>
          <div class="logItemCardBody card-body">
            <pre class="logItem card-text"></pre>
          </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script>


         var itemCounter = 0;
         var maxLogItems = 500;


         function connect() {
             var wss = new WebSocket('wss://<?php echo $wsUrl ?>');
             wss.onopen = function () {
                 console.log("Connection established! Registering session <?php echo $sessionId ?>");
                 wss.send('StdoutOnline-Register-Session <?php echo $sessionId ?>');
                 $('.connectionSuccess').removeClass('hide');
                 $('.connectionPending').addClass('hide');
             };

             wss.onmessage = function (e) {
                 itemCounter++;
                 var $logItemToClone = $('div.logItemContainer.toClone');
                 $logItemToClone
                     .clone()
                        .removeClass('toClone hide')
                        .addClass('countableItem')
                        .insertAfter($logItemToClone)
                        .find('.logItem')
                            .text(JSON.parse(e.data))
                        .end()
                        .find('.timestamp')
                            .html(getDateTimeStr() + '<small>.' + getCurrentMilliseconds() + '</small>')
                            .find('.logItemCounter')
                                .text(itemCounter)
                        .end()
                        .find('.logItemCardHeader, .logItemCardBody')
                            .addClass('highlightBg')
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

         $(function(){
             $(document).on('click', '.logItemHeader', function(){
                 $(this).parent().find('.logItem').toggleClass('collapse')
             });
         });
    </script>
  </body>
</html>