<?php

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

        .card.cardDark {
            background-color: #2d2d2d;
            color: whitesmoke;
            max-width: 30rem;
        }

    </style>

  </head>
  <body>
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
          <div class="row justify-content-md-center">
              <div class="card cardDark sessionSetup">
                  <div class="card-body">
                      <label for="sessionId">Your session id</label>
                      <div class="input-group mb-3">
                          <input type="text" class="sessionIdInput form-control" id="sessionId">
                          <div class="input-group-append">
                              <button class="btn btn-primary" id="startLogging" type="button">Start logging</button>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <div class="row justify-content-md-center">
              <div class="card cardDark explanation">
                  <div class="card-body">
                      <h5 class="text-center">What's this?</h5>
                      <p>This website provides a way to send arbitrary text to the browser from any program with zero setup.</p>
                      <h5 class="text-center">How does it work</h5>
                      <p>Click on the "Start logging" button above, copy the code snippet and run it anywhere in your program. The output will show up in your browser.</p>
                      <h5 class="text-center">Y tho</h5>
                      <p>This is the kind of tool which I "shouldn't" need, but nevertheless over the years I found myself in situations where it would have been a godsend.
                          On multiple occasions I urgently needed to debug something but I didn't have a quick way to output stuff - maybe I didn't have ssh access,
                          maybe the script ran in the background and the output was redirected to /dev/null, maybe logging was not injected in the class I was looking at...
                          In each case it probably only took a few minutes to find a workaround, but those were minutes I would have preferred to spend actually debugging.
                          Being able to just paste a one-liner literally anywhere in the code to dump any value I want is exactly what I needed. So I decided to write an app
                          that allows me to do exactly that.
                      </p>
                      <h5 class="text-center">Is it safe?</h5>
                      <p>
                          No. Don't use it for any sensitive data for two reasons:
                          <ul>
                              <li>Anyone who guesses your session id can see your log output and you won't know about it.</li>
                              <li>Any data you send to the stdout.online TCP server may end up in logs and memory dumps. Having said that, nothing is stored on purpose server side.</li>
                          </ul>
                      </p>
                  </div>
              </div>
          </div>

      </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script>
        var serverUnique = "<?= substr(uniqid('', false),-4) ?>";
        var clientUnique = Math.random().toString(36).substring(4, 12)

        $(function(){
            $('#sessionId').val(serverUnique + clientUnique);

            $(document).on('click', '#startLogging', function(){
                var sessionId = $('#sessionId').val();
                if (sessionId.length > 0) {
                    location.href = '/log?id=' + sessionId;
                } else {
                    alert('Session id cannot be empty');
                }
                e.preventDefault();
            });
        });
    </script>
  </body>
</html>