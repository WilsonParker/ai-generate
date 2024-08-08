<!DOCTYPE html>
<head>
    <title>Pusher Test</title>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    <script> // the channel name is the same as the one in the event
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;
        var pusher = new Pusher('c20a1c4b96cade20154f', {
            cluster: 'ap3'
        });

        var channel = pusher.subscribe('users.1');
        channel.bind('stock.generated', function (data) {
            alert(JSON.stringify(data));
        });
    </script>
</head>
<body>
<h1>Pusher Test</h1>
<p>
    Try publishing an event to channel <code>my-channel</code>
    with event name <code>my-event</code>.
</p>
</body>
