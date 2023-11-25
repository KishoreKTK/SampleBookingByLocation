<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap" rel="stylesheet">
    <title>Mood</title>
    <style>
        html,
        body,
        div,
        span,
        applet,
        object,
        iframe,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p,
        blockquote,
        pre,
        a,
        abbr,
        acronym,
        address,
        big,
        cite,
        code,
        del,
        dfn,
        em,
        img,
        ins,
        kbd,
        q,
        s,
        samp,
        small,
        strike,
        strong,
        sub,
        sup,
        tt,
        var,
        b,
        u,
        i,
        center,
        dl,
        dt,
        dd,
        ol,
        ul,
        li,
        fieldset,
        form,
        label,
        legend,
        table,
        caption,
        tbody,
        tfoot,
        thead,
        tr,
        th,
        td,
        article,
        aside,
        canvas,
        details,
        embed,
        figure,
        figcaption,
        footer,
        header,
        hgroup,
        menu,
        nav,
        output,
        ruby,
        section,
        summary,
        time,
        mark,
        audio,
        video {
            margin: 0;
            padding: 0;
            border: 0;
            font-size: 100%;
            font: inherit;
            vertical-align: baseline;
        }

        /* HTML5 display-role reset for older browsers */
        article,
        aside,
        details,
        figcaption,
        figure,
        footer,
        header,
        hgroup,
        menu,
        nav,
        section {
            display: block;
        }

        body {
            line-height: 1;
        }

        ol,
        ul {
            list-style: none;
        }

        blockquote,
        q {
            quotes: none;
        }

        blockquote:before,
        blockquote:after,
        q:before,
        q:after {
            content: '';
            content: none;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
        }

        *,
        ::after,
        ::before {
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            font-size: 13px;
            line-height: 24px;
            font-weight: 400;
        }
    </style>

</head>
<table align="center" width="95%" style="margin: 0 auto;">

    <tbody>


        <tr>
            <td align="left" style="padding:10px 10px;;">







            </td>

        </tr>


    </tbody>


</table>
<table align="center" width="95%" style="margin: 0 auto;">
    <tbody>
        <tr>
            <td style="padding:10px 20px;color: #fff;
            background-color: #A2654D;
            border-color: #A2654D;"><a href=""><img style="width:155px; height:42px;" src="{{ asset('img/logo/mood-white-logo.png') }}" alt=""></a></td>
        </tr>
    </tbody>


</table>
<table align="center" width="95%" style="margin: 0 auto;">

    <tbody>


        <tr>
            <td align="left" style="padding:10px 10px;;">







            </td>

        </tr>


    </tbody>


</table>
<table align="center" width="95%" style="margin: 0 auto;">
    <tbody>
        <tr>
            <td align="left" style="padding:10px 20px;font-size: 13px;font-family: 'Roboto', sans-serif;">
                <h6 style="font-size: 14px; font-weight:500;">
                    <font face="'Roboto', sans-serif">Mood</font>
                </h6>


                <font face="'Roboto', sans-serif">Mood Portal,
                    Villa 10, Bur Dubai,
                    Dubai, UAE <br> info@mood.com<br>

                    +971 55 555 5555
                </font>

            </td>



            <td align="right" style="padding:10px 20px;font-family: 'Roboto', sans-serif;">
                <h3 style="font-size: 22px; font-weight:700;">
                    <font face="'Roboto', sans-serif">INVOICE</font>
                </h3>



            </td>


        </tr>
        <tr>
            <td align="left" style="padding:10px 20px;font-size: 13px;font-family: 'Roboto', sans-serif;">
                <h6 style="font-size: 14px; font-weight:500;">
                    <font face="'Roboto', sans-serif">BILL TO</font>
                </h6>

                <font face="'Roboto', sans-serif">{{$details->first_name}} {{$details->last_name}}<br> {{$details->address}}
                </font>

            </td>



            <td align="right" style="padding:10px 20px;font-family: 'Roboto', sans-serif;">
                <h6 style="font-size: 14px; font-weight:500;">
                    <font face="'Roboto', sans-serif">INVOICE ID: {{$details->orderId}}</font>
                </h6>
                <h6 style="font-size: 14px; font-weight:500;">
                    <font face="'Roboto', sans-serif">INVOICE DATE: {{$today}}</font>
                </h6>



            </td>


        </tr>

    </tbody>


</table>
<table align="center" width="95%" style="margin: 0 auto;">

    <tbody>


        <tr>
            <td align="left" style="padding:10px 10px;;">







            </td>

        </tr>


    </tbody>


</table>
<table align="center" width="95%" style="margin: 0 auto;">
    <thead stya>
        <tr>
            <th align="left" style="padding:6px 10px;color: #fff;
            background-color: #A2654D;
            border-color: #A2654D; text-align: left;font-size: 13px;">
                <font face="'Roboto', sans-serif">{{$details->salon_name}}</font>
            </th>
            <th align="right" style="padding:6px 10px;color: #fff;
              background-color: #A2654D;
              border-color: #A2654D; text-align: right;font-size: 13px;">
                <font face="'Roboto', sans-serif">PRICE</font>
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach($book_services as $index=>$service)

        <tr>

            <td align="left" style="padding:6px 10px;
                      text-align: left;font-size: 13px;border-bottom: 1px solid #dee2e6;">
                <h6 style="font-size: 13px; font-weight:500; text-transform:uppercase;">
                    <font face="'Roboto', sans-serif"></font>
                </h6>


                <span style="font-size: 13px; float:left;">
                    <font face="'Roboto', sans-serif">
                        {{$service->service}}:
                    </font>
                </span>

                <span style="font-size: 13px; float:right;">
                    <font face="'Roboto', sans-serif">
                        {{$service->date}} ( {{$service->start_time}} - {{$service->end_time}}),
                    </font>
                </span>


            </td>

            <td align="right" style="padding:6px 10px;
                          text-align: right;font-size: 13px;border-bottom: 1px solid #dee2e6;">
                @if(isset($service->discount_price)&& $service->discount_price < $service->amount)
                    <font face="'Roboto', sans-serif">{{$service->discount_price}}&nbsp;&nbsp;<strike style="font-size:12px">{{$service->amount}}</strike> AED</font>
                    @else
                    <font face="'Roboto', sans-serif">{{$service->amount}} AED</font>
                    <!-- <font face="'Roboto', sans-serif">{{$service->discount_price}}<strike>{{$service->amount}}</strike> AED</font> -->
                    @endif

            </td>

        </tr>
        @endforeach

        <tr>
            <td align="left" style="padding:6px 10px;
                              text-align: left;font-size: 13px;border-bottom: 1px solid #dee2e6;">
                <h6 style="font-size: 13px; font-weight:500;">
                    <font face="'Roboto', sans-serif">SUB TOTAL</font>
                </h6>


            </td>
            <td align="right" style="padding:6px 10px;
                                  text-align: right;font-size: 13px;border-bottom: 1px solid #dee2e6;">
                <font face="'Roboto', sans-serif">{{$details->amount_total}} AED</font>
            </td>
        </tr>
        <tr>
            <td align="left" style="padding:6px 10px;
                                  text-align: left;font-size: 13px;border-bottom: 1px solid #dee2e6;">
                <h6 style="font-size: 13px; font-weight:500;">
                    <font face="'Roboto', sans-serif">TOTAL</font>
                </h6>


            </td>
            <td align="right" style="padding:6px 10px;
                                      text-align: right;font-size: 13px;border-bottom: 1px solid #dee2e6;">
                <font face="'Roboto', sans-serif">{{$details->amount_paid}} AED</font>
            </td>
        </tr>


    </tbody>

</table>

<table align="center" width="95%" style="margin: 0 auto;">

    <tbody>


        <tr>
            <td align="left" style="padding:10px 10px;;">







            </td>

        </tr>


    </tbody>


</table>
<table align="center" width="95%" style="margin: 0 auto;">

    <tbody>


        <tr>
            <td align="left" style="padding:10px 10px;;">







            </td>

        </tr>


    </tbody>


</table>
<table align="center" width="95%" style="margin: 0 auto;">

    <tbody>


        <tr>
            <td align="left" style="padding:6px 10px;
                                      text-align: left;font-size: 13px;">
                <h6 style="font-size: 13px; font-weight:500; text-align: center;">
                    <font face="'Roboto', sans-serif"><a style="color: #000000;" href="https://www.mood.ae/terms-and-conditions" target="_blank">Terms & Conditions</a>&nbsp;&nbsp;&nbsp;<a style="color: #000000;" href="https://www.mood.ae/privacy-policy" target="_blank">Privacy Policy</a></font>
                </h6>



                <!-- <span style="font-size: 13px;">
                    <font face="'Roboto', sans-serif">{!!$terms!!}
                    </font>
                </span> -->







            </td>

        </tr>


    </tbody>


</table>
<!-- <table align="center" width="95%" style="margin: 0 auto;">

    <tbody>


        <tr>
            <td align="left" style="padding:6px 10px;
                                      text-align: left;font-size: 13px;">
                <h6 style="font-size: 13px; font-weight:500;">
                    <font face="'Roboto', sans-serif">TERMS & CONDITIONS</font>
                </h6>



                <span style="font-size: 13px;">
                    <font face="'Roboto', sans-serif">{!!$terms!!}
                    </font>
                </span>







            </td>

        </tr>


    </tbody>


</table> -->
<table align="center" width="95%" style="margin: 0 auto;">

    <tbody>


        <tr>
            <td align="left" style="padding:10px 10px;;">







            </td>

        </tr>


    </tbody>


</table>
</body>

</html>
