<!DOCTYPE html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

    <script type="text/javascript">

        window.onload = function() {
            var reloading = sessionStorage.getItem("reloading");
            if (reloading) {
                sessionStorage.removeItem("reloading");
                $("#messages").html('Content has been refreshed');
            }

            $( ".clickable" ).click(function() {
                $( this ).css("color", "red");
            });
        }

        setInterval(function() {
            $("#messages").html('Refreshing...');
            sessionStorage.setItem("reloading", "true");
            window.location.reload();
        }, 10000 );

    </script>
</head>
<body>

<div id="messages"></div>

<div id="posts">
@foreach ($posts as $post)
    <div class="clickable">
        <div>{{ $post->text }}</div>
        <div>{{ $post->user->screen_name }}</div>
        <div>{{ $post->created_at }}</div>
    </div>
@endforeach
</div>

</body>
</html>