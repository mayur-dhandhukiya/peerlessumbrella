<html>
    <head>
        <meta name="viewport" content="width=device-width">
    </head>
    <body style="background:#000;overflow: hidden;height:100%;margin:0 auto;"> 
        <video controls="loop" autoplay="<?php echo $_GET['autoplay'];?>" name="media" style="width: 100%;height: 100%;">
            <source src="<?php echo $_GET['video_link'];?>">
            <h3 style="display:table;vertical-align: center; color:#fff;margin-top:20%;">Your browser does not support the HTML5 video tag. Try updating your browser or using a different one.</h3>']
        </video>
    </body>
</html>