<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?php echo $title; ?></title>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script type="text/javascript">
        function postLaboNow()
        {
            $.ajax({
                type: "POST",
                url: "./labo_now",
                datatype: 'json',
            }).done(function(data) {
                console.log('Success. ReturnCode:' + data['code']);
            }).fail(function(data) {
                console.log('Fail');
            });
        }
        </script>
    </head>
    <body>
        <button type="button" name="post" onclick="postLaboNow();"><?php echo $button_text; ?></button>
    </body>
</html>