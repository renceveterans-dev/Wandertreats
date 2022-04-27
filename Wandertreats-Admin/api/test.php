<!DOCTYPE html>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
<?php ?>
$(document).ready(function(){
  $("button").click(function(){
        $.ajax({

            url: "https://wanderlustphtravel.com/wandertreats/api_send_email.php",
            cache: true,
            method: "POST",
            data:{
            
                  sender : 'HAHA',
                  sender_email :  'laurencevegerano@gmail.com',
                  recipient : 'HEHE',
                  recipient_email : 'wanderlustph.traveldeals@gmail.com',
                  subject : 'Hey',
                  cc_email: 'laurencevegerano@gmail.com',
                  message : 'Hello Word'
            },
            success: function(data) {
                console.log(data);
            },
            error: function (xhr, ajaxOptions, thrownError) {
            
                alert("Error :"+xhr.status);
                
            }
        });
    });
});
</script>
</head>
<body>

<div id="div1"><h2>Let jQuery AJAX Change This Text</h2></div>

<button>Get External Content</button>

</body>
</html>
