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
        <noscript>
            <div class="hideOnJs row justify-content-center">
                <div class="card cardDarkNarrow">
                    <div class="card-body">
                        <h4 class="jumbotron text-danger">This website needs javascript.</h4>
                    </div>
                </div>
            </div>
        </noscript>
        <div class="row justify-content-center">
            <div class="sessionSetup card cardDarkNarrow">
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
                        setup.
                    </p>
                    <hr/>

                    <h5 class="text-center">How do I use it?</h5>
                    <p>Click on the "Start logging" button above, copy the code snippet and run it anywhere in your
                        program. The output will show up in your browser.
                    </p>
                    <hr/>

                    <h5 class="text-center">How does it work</h5>
                    <p>The code snippet you can copy after you click "Start logging" above sends a request to the
                        https://stdout.online/log endpoint. The web server server passes it to a WebSocket server which
                        your browser is connected to. That means you can use stdout.online with any programming language
                        capable of making HTTPS or even just plain TCP* connections.
                        The maximum number of logged messages is 1000. After reaching this limit the oldest messages
                        will be deleted as new messages arrive. If you want to keep a message, you can pin it.
                        Refreshing the page will clear all messages with no way to recover them.

                        *A TCP server is available if you cannot use HTTPS, but keep in mind the connection is not
                        encrypted. The server is available at tcp://stdout.online:10660.
                    </p>
                    <hr/>

                    <h5 class="text-center">Is it safe?</h5>
                    <p>
                        No. While all the connections are encrypted (unless you use the TCP server), you shouldn't use
                        it for any sensitive data for two reasons:
                    </p>
                    <ul>
                        <li>
                            Anyone who guesses your session id can see your log output and you won't know about it.
                        </li>
                        <li>
                            Any data you send to the stdout.online HTTPS or TCP server may end up in logs and memory
                            dumps. Having said that, nothing is intentionally stored on the server.
                        </li>
                    </ul>
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
    <header class="topNavbar container-fluid">
        <div class="page-header">
            <h5 class="headerFlex">
                <span>
                    stdout.online
                    <span class="badge badge-success hide connectionSuccess">Connected</span>
                    <span class="badge badge-warning connectionPending">Connecting...</span>
                    <span class="badge badge-info sessionIdBadge"></span>
                </span>
                <span class="marginLeftAuto">
                    <a href="#" class="getCodeLink"><small>Get code</small></a>
                    <a href="#" class="freezeLogLink"><small>Freeze</small></a>
                </span>
            </h5>
        </div>
    </header>

    <div class="pinnedItems"></div>

    <hr/>

    <div class="codeSnippetsWrapper container-fluid ">
        <div class="card">
            <div class="card-header">
                <div class="flexbox">
                    <p class="codeSnippetsExplanation">Choose your programming language and use the provided code snippet
                        to send log messages to this window.</p>
                    <a href="#" class="codeSnippetsCloseLink">Close</a>
                </div>
                <ul class="nav nav-pills" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-php" role="tab"
                           aria-controls="pills-home" aria-selected="true">PHP 5.4+</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-python2" role="tab"
                           aria-controls="pills-contact" aria-selected="false">Python 2</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-python3" role="tab"
                           aria-controls="pills-contact" aria-selected="false">Python 3</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-ruby" role="tab"
                           aria-controls="pills-profile" aria-selected="false">Ruby</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-other" role="tab"
                           aria-controls="pills-contact" aria-selected="false">Other</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-php" role="tabpanel" aria-labelledby="pills-home-tab">
                        <pre class="codeToCopy">
call_user_func(function($m){file_get_contents('https://stdout.online/log/',0,stream_context_create(['http'=>['header'=>['Content-Type:'],'content'=>json_encode(['m'=>$m,'s'=>'{%%sessionId%%}'])]]));},
'Your message goes here');</pre>
                        <a href="#" class="copyCodeLink">Copy code snippet</a>
                        <span class="badge badge-success copyCodeSuccess" style="display:none;">Copied!</span>
                    </div>
                    <div class="tab-pane fade" id="pills-python2" role="tabpanel" aria-labelledby="pills-contact-tab">
                        <pre class="codeToCopy">
def stdout_online(m):
    import urllib;import json;urllib.urlopen('https://stdout.online/log/',json.dumps({'m':m,'s':'{%%sessionId%%}'}))
stdout_online("Your message goes here")</pre>
                        <a href="#" class="copyCodeLink">Copy code snippet</a>
                        <span class="badge badge-success copyCodeSuccess" style="display:none;">Copied!</span>
                    </div>
                    <div class="tab-pane fade" id="pills-python3" role="tabpanel" aria-labelledby="pills-contact-tab">
                        <pre class="codeToCopy">
def stdout_online(m):
    import urllib.request;import json;urllib.request.urlopen('https://stdout.online/log/',bytes(json.dumps({'m':m,'s':'{%%sessionId%%}'}),'UTF-8'))
stdout_online("Your message goes here")</pre>
                        <a href="#" class="copyCodeLink">Copy code snippet</a>
                        <span class="badge badge-success copyCodeSuccess" style="display:none;">Copied!</span>
                    </div>
                    <div class="tab-pane fade" id="pills-ruby" role="tabpanel" aria-labelledby="pills-profile-tab">
                        <pre class="codeToCopy">
Proc.new{|m|require 'net/http';r=Net::HTTP::Post.new('/log/');r.body={'m'=>m,'s'=>'{%%sessionId%%}'}.to_json;h=Net::HTTP.new('stdout.online',443);h.use_ssl=true;h.request(r)}
.call('Your message goes here')</pre>
                        <a href="#" class="copyCodeLink">Copy code snippet</a>
                        <span class="badge badge-success copyCodeSuccess" style="display:none;">Copied!</span>
                    </div>
                    <div class="tab-pane fade" id="pills-other" role="tabpanel" aria-labelledby="pills-contact-tab">
                        <pre>coming soon</pre>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <div class="logItemContainer container-fluid removableItem originalItem toClone hide">
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

    <div class="freezeItem container-fluid removableItem toClone hide">
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