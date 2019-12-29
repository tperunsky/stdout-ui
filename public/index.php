<?php
$config = require __DIR__ . '/../config/config.php';
$host = $config['websocketServer']['host'];
$port = $config['websocketServer']['port'];
$wssUrl = $host . ':' . $port;
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
    <link rel="icon" type="image/png" sizes="192x192" href="/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="/styles.css">

    <title>stdout.online</title>
</head>
<body>
<div class="welcome">
    <div class="container-fluid">
        <header class="container-fluid">
            <div class="page-header">
                <h4 class="headerFlex">
                    <span>
                        stdout.online
                    </span>
                </h4>
            </div>
        </header>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="card cardDarkNarrow sessionSetup">
                <div class="card-body">
                    <label for="sessionId">Your session id</label>
                    <div class="input-group mb-3">
                        <input type="text" class="sessionIdInput form-control" id="sessionId">
                        <div class="input-group-append">
                            <button class="btn btn-primary" id="startLogging" type="button">Start logging</button>
                        </div>
                    </div>
                    <a href="#" class="lastSessionIdLink hide">
                        Use my last session id: <span class="lastSessionId"></span>
                    </a>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="card cardDarkNarrow explanation">
                <div class="card-body">
                    <h5 class="text-center">What's this?</h5>
                    <p>This website provides a way to send arbitrary text to the browser from any program with zero
                        setup.</p>
                    <hr/>
                    <h5 class="text-center">How does it work</h5>
                    <p>Click on the "Start logging" button above, copy the code snippet and run it anywhere in your
                        program. The output will show up in your browser. Your session id will be saved in local
                        storage.</p>
                    <hr/>
                    <h5 class="text-center">Is it safe?</h5>
                    <p>
                        No. Don't use it for any sensitive data for two reasons:
                    <ul>
                        <li>Anyone who guesses your session id can see your log output and you won't know about it.</li>
                        <li>Any data you send to the stdout.online TCP server may end up in logs and memory dumps.
                            Having said that, nothing is intentionally stored on the server.
                        </li>
                    </ul>
                    </p>
                    <hr/>
                    <h5 class="text-center">y tho</h5>
                    <p>This is the kind of tool which I "shouldn't" need, but nevertheless over the years I found myself
                        in situations where it would have been a godsend. On multiple occasions I urgently needed to
                        debug something but I didn't have a quick way to output stuff - maybe I didn't have ssh access,
                        maybe the script ran in the background and the output was redirected to /dev/null, maybe logging
                        was not injected in the class I was looking at... In each case it probably only took a few
                        minutes to find a workaround, but those were minutes I would have preferred to spend actually
                        debugging. Being able to just paste a one-liner literally anywhere in the code to dump any value
                        I want is exactly what I needed. So I decided to write an app that allows me to do that.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="theLog hide">
    <header class="container-fluid">
        <div class="page-header">
            <h4 class="headerFlex">
                        <span>
                            stdout.online
                            <span class="badge badge-success hide connectionSuccess">Connected</span>
                            <span class="badge badge-warning connectionPending">Connecting...</span>
                            <span class="badge badge-info sessionIdBadge"></span>
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
                <p class="codeSnippetsExplanation">Choose your programming language and use the provided code snippet
                    to send log messages to this window.</p>
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
                            <pre class="codeToCopy">
(function($m){fwrite($c=stream_socket_client('tcp://stdout.online:10660'),json_encode(['m'=>$m,'s'=>'{%%sessionId%%}']));fclose($c);})
('Your text goes here');</pre>
                        <a href="#" class="copyCodeLink">Copy code</a>

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
                          <pre class="logItem card-text"><span
                                      class="freezeText">Log frozen. All new messages are ignored.</span> Discarded messages: <span
                                      class="freezeDiscardedMessages">0</span>. <span class="unfreezeText"></span></pre>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="serverUnique" value="<?= substr(uniqid('', false), -5) ?>"/>
<input type="hidden" id="wssUrl" value="wss://<?= $wssUrl ?>"/>
<script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
<script src="/scripts.js"></script>

</body>
</html>