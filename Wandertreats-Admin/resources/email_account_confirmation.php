<?php
 $url = $_REQUEST['url']."?username=".$_REQUEST['username']."&url=".$_REQUEST['url']."&userId=".$_REQUEST['userId']."&token=".$_REQUEST['token']."";
?>
<!DOCTYPE html>
<html>

<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300&display=swap" rel="stylesheet">
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap');
        @media screen {
            @font-face {
                font-family: 'Poppins';
                font-style: normal;
                font-weight: 400;
                src: local('Poppins Regular'), local('Poppins-Regular'), url(https://fonts.gstatic.com/s/Poppins/v11/qIIYRU-oROkIk8vfvxw6QvesZW2xOQ-xsNqO47m55DA.woff) format('woff');
            }

            @font-face {
                font-family: 'Poppins';
                font-style: normal;
                font-weight: 700;
                src: local('Poppins Bold'), local('Poppins-Bold'), url(https://fonts.gstatic.com/s/Poppins/v11/qdgUG4U09HnJwhYI-uK18wLUuEpTyoUstqEm5AMlJo4.woff) format('woff');
            }

            @font-face {
                font-family: 'Poppins';
                font-style: italic;
                font-weight: 400;
                src: local('Poppins Italic'), local('Poppins-Italic'), url(https://fonts.gstatic.com/s/Poppins/v11/RYyZNoeFgb0l7W3Vu1aSWOvvDin1pK8aKteLpeZ5c0A.woff) format('woff');
            }

            @font-face {
                font-family: 'Poppins';
                font-style: italic;
                font-weight: 700;
                src: local('Poppins Bold Italic'), local('Poppins-BoldItalic'), url(https://fonts.gstatic.com/s/Poppins/v11/HkF_qI1x_noxlxhrhMQYELO3LdcAZYWl9Si6vvxL-qU.woff) format('woff');
            }
        }

        /* CLIENT-SPECIFIC STYLES */
        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            -ms-interpolation-mode: bicubic;
        }

        /* RESET STYLES */
        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        table {
            border-collapse: collapse !important;
        }

        body {
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        /* iOS BLUE LINKS */
        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        /* MOBILE STYLES */
        @media screen and (max-width:600px) {
            h1 {
                font-size: 32px !important;
                line-height: 32px !important;
            }
        }

        /* ANDROID CENTER FIX */
        div[style*="margin: 16px 0;"] {
            margin: 0 !important;
        }
    </style>
</head>

<body style="background-color: #e1e1e1; margin: 0 !important; padding: 0 !important;">
    <!-- HIDDEN PREHEADER TEXT -->
    <div style="display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: 'Poppins', Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;"> We're thrilled to have you here! Get ready to dive into your new account. </div>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <!-- LOGO -->
        <tr>
            <td bgcolor="#22a8e3" align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <tr>
                        <td align="center" valign="top" style="padding: 40px 10px 40px 10px;"> </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#22a8e3" align="center" style="padding: 0px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <tr>
                        <td bgcolor="#ffffff" align="center" valign="top" style="padding: 40px 20px 10px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: 'Poppins', sans-serif; font-size: 18px; font-weight: 400; letter-spacing: 4px; line-height: 18px;">
                            <h1 style="font-size: 16px; font-weight: 400; margin: 2;">Welcome!</h1> <img src="https://wanderlustphtravel.com/wandertreats/admin/img/icon.png" width="90" height="75" style="display: block; border: 0px;" />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#e1e1e1" align="center" style="padding: 0px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <tr>
                        <td bgcolor="#ffffff" align="left" style="padding: 10px 20px 0px 20px; color: #666666; font-family: 'Poppins', Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 14px;">
                            <p style="margin: 0;">Hi <?= $_REQUEST['username']; ?> !, We're excited to have you get started. First, you need to confirm your account. Just press the button below.</p>
                        </td>
                    
                    </tr>
                    <tr>
                        <td bgcolor="#ffffff" align="left">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td bgcolor="#ffffff" align="center" style="padding: 20px 20px 10px 20px;">
                                        <table border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td align="center" style="border-radius: 12px;" bgcolor="#22a8e3"><a href="<?=  $url; ?>" target="_blank" style="font-size: 14px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; padding: 10px 15px; border-radius: 12px; border: 1px solid #22a8e3; display: inline-block;">Confirm Account</a></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr> <!-- COPY -->
                    <tr>
                        <td bgcolor="#ffffff" align="center" style="padding: 0px 20px 0px 20px; color: #666666; font-family: 'Poppins', Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 14px;">
                            <p style="margin: 0;">If that doesn't work, copy and paste the following link in your browser:</p>
                        </td>
                    </tr> <!-- COPY -->
                    <tr>
                        <td bgcolor="#ffffff"align="center"style="padding: 20px 20px 20px 20px; color: #666666; font-family: 'Poppins', Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 14px;">
                            <p style="margin: 0;"><a href="https://prf.hn/l/da3WdWg" target="_blank" style="color: #1746e0;">https://prf.hn/l/da3WdWg</a></p>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#ffffff" align="center" style="padding: 0px 20px 20px 20px; color: #666666; font-family: 'Poppins', Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 14px;">
                            <p style="margin: 0;">If you have any questions, just reply to this email—we're always happy to help out.</p>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#ffffff" align="center" style="padding: 0px 20px 40px 20px; border-radius: 0px 0px 4px 4px; color: #666666; font-family: 'Poppins', Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 14px;">
                            <p style="margin: 0;">Cheers,<br>Wandertreats Team</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#e1e1e1" align="center" style="padding: 30px 20px 0px 20px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <tr>
                        <td bgcolor="#FFECD1" align="center" style="padding: 30px 20px 30px 20px; border-radius: 4px 4px 4px 4px; color: #666666; font-family: 'Poppins', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 14px;">
                            <h2 style="font-size: 14px; font-weight: 400; color: #111111; margin: 0;">Need more help?</h2>
                            <p style="font-size: 14px;margin: 0;"><a href="#" target="_blank" style="color: #1746e0;">We&rsquo;re here to help you out</a></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#e1e1e1" align="center" style="padding: 0px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <tr>
                        <td bgcolor="#e1e1e1" align="left" style="padding: 0px 30px 30px 30px; color: #666666; font-family: 'Poppins', Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 14px;"> <br>
                            <p style="ont-size: 14px; margin: 0;">If these emails get annoying, please feel free to <a href="#" target="_blank" style="color: #111111; font-weight: 700;">unsubscribe</a>.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>