<!DOCTYPE html>
<html>
    <head>
        <title>mail test</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="{{asset('css/style.css')}}">
    </head>
    <body>
        <div class="main">
            <div class="section-img">
                <img src="{{$details['logo']}}" alt="logo" width="100">
            </div>
            <div class="section-msg">
                <h3>Hi, {{$details['name']}}!</h3>
                <p style="margin: 8px 0 28px 0;">Please enter the code below to verify your email address.</p>
                <div>
                    <input type="text" id="code" readonly value="{{$details['code']}}" class="input-code">
                    <span id="copy" class="copy-code">Code Copied</span>
                </div>
                <p style="margin: 20px 0 15px 0;">
                    If you did not create an account, no further action is required.
                </p>
                <p>Regards,<br>{{$details['team']}}</p>
            </div>
            <p class="foot">&copy; 2021 {{$details['app']}}. All rights reserved.</p>
        </div>

        <script src="{{asset('js/script.js')}}"></script>
    </body>
</html>
