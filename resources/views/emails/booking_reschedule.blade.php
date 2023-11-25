<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="format-detection" content="telephone=no" />
    <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap" rel="stylesheet">
    <title>Booking Reschedule</title>
    <style type="text/css">
        body {
            width: 100% !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
        }

        .ReadMsgBody {
            width: 100%;
        }

        .ExternalClass {
            width: 100%;
        }

        .backgroundTable {
            margin: 0 auto;
            padding: 0;
            width: 100% !important;
        }

        .ExternalClass * {
            line-height: 115%;
        }

        p {
            margin: 0;
        }

        div.tpl-content {
            padding: 0 !important;
        }

        span.preheader {
            display: none;
            font-size: 1px;
            visibility: hidden;
            opacity: 0;
            color: transparent;
            height: 0;
            width: 0;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        table,
        td {
            border-collapse: collapse;
        }

        *[class="sm-show"],
        .sm-show {
            display: none;
            visibility: hidden
        }

        @media only screen and (max-width: 795px) {

            *[class="mobile-column"],
            .mobile-column {
                display: block;
            }

            *[class="mob-column"],
            .mob-column {
                float: none !important;
                width: 100% !important;
            }

            *[class="hide"],
            .hide {
                display: none !important;
            }

            *[class="condensed"],
            .condensed {
                padding-bottom: 40px !important;
                display: block;
            }

            *[class="center"],
            .center {
                text-align: center !important;
                width: 100% !important;
                height: auto !important;
            }

            *[class="100pad"] {
                width: 100% !important;
                padding: 20px;
            }

            *[class="100padleftright"] {
                width: 100% !important;
                padding: 0 20px 0 20px;
            }

            *[class="100padtopbottom"] {
                width: 100% !important;
                padding: 20px 0 20px 0;
            }

            *[class="hr"],
            .hr {
                width: 100% !important;
            }

            *[class="p10"],
            .p10 {
                width: 10% !important;
                height: auto !important;
            }

            *[class="p20"],
            .p20 {
                width: 20% !important;
                height: auto !important;
            }

            *[class="p30"],
            .p30 {
                width: 30% !important;
                height: auto !important;
            }

            *[class="p40"],
            .p40 {
                width: 40% !important;
                height: auto !important;
            }

            *[class="p50"],
            .p50 {
                width: 50% !important;
                height: auto !important;
            }

            *[class="p60"],
            .p60 {
                width: 60% !important;
                height: auto !important;
            }

            *[class="p70"],
            .p70 {
                width: 70% !important;
                height: auto !important;
            }

            *[class="p80"],
            .p80 {
                width: 80% !important;
                height: auto !important;
            }

            *[class="p90"],
            .p90 {
                width: 90% !important;
                height: auto !important;
            }

            *[class="p100"],
            .p100 {
                width: 100% !important;
                height: auto !important;
            }

            *[class="p15"],
            .p15 {
                width: 15% !important;
                height: auto !important;
            }

            *[class="p25"],
            .p25 {
                width: 25% !important;
                height: auto !important;
            }

            *[class="p33"],
            .p33 {
                width: 33% !important;
                height: auto !important;
            }

            *[class="p35"],
            .p35 {
                width: 35% !important;
                height: auto !important;
            }

            *[class="p45"],
            .p45 {
                width: 45% !important;
                height: auto !important;
            }

            *[class="p55"],
            .p55 {
                width: 55% !important;
                height: auto !important;
            }

            *[class="p65"],
            .p65 {
                width: 65% !important;
                height: auto !important;
            }

            *[class="p75"],
            .p75 {
                width: 75% !important;
                height: auto !important;
            }

            *[class="p85"],
            .p85 {
                width: 85% !important;
                height: auto !important;
            }

            *[class="p95"],
            .p95 {
                width: 95% !important;
                height: auto !important;
            }

            *[class="alignleft"] {
                text-align: left !important;
            }

            *[class="100button"] {
                width: 100% !important;
            }

            *[class="mob-auto"],
            .mob-auto {
                width: auto !important;
                height: auto !important;
            }

            *[class="sm-show"],
            .sm-show {
                display: initial !important;
                visibility: visible !important;
            }

            *[class="sm-no-border"],
            .sm-no-border {
                border-left: 0 !important;
                border-top: 0 !important;
                border-bottom: 0 !important;
                border-right: 0 !important;
            }
        }

        @media only screen and (max-width: 450px) {

            *[class="xs-no-pad"],
            .xs-no-pad {
                padding: 0 !important;
            }

            *[class="xs-p25"],
            .xs-p25 {
                width: 25% !important;
                height: auto !important;
            }

            *[class="xs-p50"],
            .xs-p50 {
                width: 50% !important;
                height: auto !important;
            }

            *[class="xs-p75"],
            .xs-p75 {
                width: 75% !important;
                height: auto !important;
            }

            *[class="xs-p100"],
            .xs-p100 {
                width: 100% !important;
                height: auto !important;
            }

            *[class="xs-hide"],
            .xs-hide {
                display: none !important;
            }

            *[class="xs-header"],
            .xs-header {
                font-size: 45px !important
            }
        }
    </style>
</head>

<body
    style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background-color: #ffffff; margin: 0; padding: 0; padding-top:40px; padding-bottom:40px; width: 100% !important;">

    <span class="preheader"
        style="color: transparent; display: none; font-size: 1px; height: 0; opacity: 0; visibility: hidden; width: 0;">Mood Booking Reschedule</span>
		<table
        style="background-color: #ffffff; margin: 0; mso-table-lspace: 0; mso-table-rspace: 0; padding: 0; width: 100%;"
        cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" width="100%">
        <tr>
            <td align="center" valign="top">
                <table class="p100"
                    style="background-color: #A2654D; margin: 0; mso-table-lspace: 0; mso-table-rspace: 0; padding: 0; width: 800px;"
                    width="800" cellspacing="0" cellpadding="0" border="0" bgcolor="#cc9c83">
                    <tr>
                        <td style="width: 30px; font-size: 1px;" width="30" valign="top" align="left">&nbsp;</td>
                        <td align="center" valign="top">
                            <table class="p100"
                                style="margin: 0; mso-table-lspace: 0; mso-table-rspace: 0; padding: 0; width: 600px;"
                                width="600" cellspacing="0" cellpadding="0" border="0" align="center">
                                <tr>
                                    <td style="height: 15px; line-height: 15px; font-size: 1px; mso-line-height-rule: exactly;"
                                        valign="top" align="left">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="left" valign="top">

                                        <table class="p100"
                                            style="margin: 0; mso-table-lspace: 0; mso-table-rspace: 0; padding: 0; width: 600px;"
                                            width="600" cellspacing="0" cellpadding="0" border="0" align="left">
                                            <tr>
                                                <td align="center" valign="middle">
                                                    <table class="p100"
                                                        style="margin: 0; mso-table-lspace: 0; mso-table-rspace: 0; padding: 0; width: 600px;"
                                                        width="600" cellspacing="0" cellpadding="0" border="0"
                                                        align="center">
                                                        <tr>
                                                            <td style="height: 40px; line-height: 40px; mso-line-height-rule: exactly;"
                                                                valign="middle" align="left">

                                                                <a href="#"
                                                                    style="border: none; color: inherit; display: block; font-family: 'Roboto', sans-serif; font-size: inherit; outline: none; text-decoration: none;">
                                                                    <img src="{{ asset('img/logo/mood-white-logo.png') }}" alt="image"
                                                                        style="-ms-interpolation-mode: bicubic; border: 0; display: block; outline: 0; text-decoration: none; width: 180px;"
                                                                        width="180" border="0" />
                                                                </a>
                                                            </td>
                                                        </tr>



                                                    </table>
                                                </td>
                                            </tr>

                                        </table>

                                    </td>
                                </tr>
                                <tr>
                                    <td style="height: 15px; line-height: 15px; font-size: 1px; mso-line-height-rule: exactly;"
                                        valign="top" align="left">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 30px; font-size: 1px;" width="30" valign="top" align="left">&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>


    <table
        style="background-color: #ffffff; margin: 0; mso-table-lspace: 0; mso-table-rspace: 0; padding: 0; width: 100%;"
        cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" width="100%">
        <tr>
            <td align="center" valign="top">
                <table class="p100"
                    style="background-color: #A2654D; margin: 0; mso-table-lspace: 0; mso-table-rspace: 0; padding: 0; width: 800px;"
                    width="800" cellspacing="0" cellpadding="0" border="0" bgcolor="#cc9c83">
                    <tr>
                        <td align="center" valign="top">
                            <table class="p100"
                                style="margin: 0; mso-table-lspace: 0; mso-table-rspace: 0; padding: 0; width: 660px;"
                                width="660" cellspacing="0" cellpadding="0" border="0" align="center">
                                <tr>
                                    <td style="width: 30px; font-size: 1px;" width="30" valign="top" align="left">&nbsp;
                                    </td>
                                    <td align="center" valign="top">
                                        <table class="p100"
                                            style="margin: 0; mso-table-lspace: 0; mso-table-rspace: 0; padding: 0; width: 600px;"
                                            width="600" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td class="space-top"
                                                    style="height: 12px; line-height: 12px; font-size: 1px; mso-line-height-rule: exactly;"
                                                    valign="top" align="left">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td align="center" valign="middle">

                                                    <table class="p100"
                                                        style="margin: 0; mso-table-lspace: 0; mso-table-rspace: 0; padding: 0; width: 600px;"
                                                        width="600" cellspacing="0" cellpadding="0" border="0"
                                                        align="left">
                                                        <tr>
                                                            <td style="color: #ffffff; font-family: 'Roboto', sans-serif; font-size: 13px; font-weight: normal; letter-spacing: 0.02em; line-height: 20px; text-align: center; mso-line-height-rule: exactly;"
                                                                valign="top" align="left">

                                                                <font face="'Roboto', sans-serif">&copy; <script
                                                                        type="text/javascript ">
                                                                        document.write(new Date().getFullYear());
                                                                    </script> Mood. All right reserved.</font>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="height: 10px; line-height: 10px; font-size: 1px; mso-line-height-rule: exactly;"
                                                                valign="top" align="left">&nbsp;</td>
                                                        </tr>
                                                    </table>

                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="space-bottom"
                                                    style="height: 5px; line-height: 5px; font-size: 1px; mso-line-height-rule: exactly;"
                                                    valign="top" align="left">&nbsp;</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="width: 30px; font-size: 1px;" width="30" valign="top" align="left">&nbsp;
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
