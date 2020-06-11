<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }

        .email-table-wrapper, .email-table-wrapper *:not(html):not(style):not(br):not(tr):not(code) {
            font-family: Avenir, Helvetica, sans-serif;
            box-sizing: border-box;
        }

        .email-table-wrapper {
            background-color: {{ $header_footer_color }};
            color: #74787E;
            height: 100%;
            hyphens: auto;
            line-height: 1.4;
            margin: 0;
            -moz-hyphens: auto;
            -ms-word-break: break-all;
            width: 100% !important;
            -webkit-hyphens: auto;
            -webkit-text-size-adjust: none;
            word-break: break-all;
            word-break: break-word;
        }

        .email-body-content {
            background-color: #FFFFFF;
            border-bottom: 1px solid #EDEFF2;
            border-top: 1px solid #EDEFF2;
            margin: 0;
            padding: 0;
            width: 100%;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            -premailer-width: 100%;
        }

        .email-inner-body {
            background-color: #FFFFFF;
            margin: 0 auto;
            padding: 0;
            width: 570px;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            -premailer-width: 570px;
        }

        .email-content-cell {
            padding: 35px;
        }

        .email-header {
            padding: 25px 0;
            text-align: center;
        }

        .email-header a {
            color: #bbbfc3;
            font-size: 19px;
            font-weight: bold;
            text-decoration: none;
            text-shadow: 0 1px 0 white;
        }
        .email-footer {
            margin: 0 auto;
            padding: 0;
            text-align: center;
            width: 570px;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            -premailer-width: 570px;
        }

        .email-footer p {
            color: #AEAEAE;
            font-size: 12px;
            text-align: center;
        }

        .re-email-content-cell {
            padding:2px;
        }

        .btn {
            display: inline-block;
            font-weight: 400;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            border: 1px solid transparent;
            padding: 0.375rem 0.75rem;
            /* font-size: 0.9rem; */
            line-height: 1.6;
            border-radius: 0.25rem;
            -webkit-transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            text-decoration: none;
        }

        .btn-danger {
            color: #fff !important;
            background-color: #e3342f;
            border-color: #e3342f;
        }

        .btn-danger:hover {
            color: #fff !important;
            background-color: #d0211c;
            border-color: #c51f1a;
        }

        .btn-primary {
            background-color: #1ab394;
            border-color: #1ab394;
            color: #FFFFFF !important;
        }

        .btn-primary:hover {
            background-color: #18a689;
            border-color: #18a689;
            color: #FFFFFF !important;
            box-shadow: none;
        }
    </style>
    @stack('styles')
</head>
<body>
    <table class="email-table-wrapper" width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center">
            <table class="email-table-content" width="95%" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="email-header" style="font-size:{{$header_text_size}}">
                        <div>
                            <div style="display:inline-block">
                                @if($header_image)
                                    <img src="{{ $header_image }}" alt="" style="max-width:100px;margin-right:10px;vertical-align:middle;">
                                @endif
                            </div>
                            <div style="display:inline-block;vertical-align:middle;">
                                {!! $header_text !!}
                            </div>
                        </div>
                    </td>
                </tr>

                <!-- Email Body -->
                <tr style="font-size:{{$body_text_size}}">
                    <td class="email-body-content" width="100%" cellpadding="0" cellspacing="0">
                        <table class="email-inner-body" align="center" width="570" cellpadding="0" cellspacing="0">
                            <!-- Body content -->
                            <tr>
                                <td class="email-content-cell">
                                    @if(isset($content))
                                        {!! $content !!}
                                    @endif

                                    @yield('content')
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td>
                        <table class="email-footer" align="center" width="570" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="email-content-cell" align="center">
                                    {!! $footer_text !!}
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
