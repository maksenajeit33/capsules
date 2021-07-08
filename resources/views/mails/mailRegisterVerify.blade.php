<!DOCTYPE html>
<html>
    <head>
        <title>mail test</title>
        <meta charset="utf-8">
    </head>
    <body style="background-color: #edf2f7;color: #333;font-family: BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
        <div style="max-width: 500px;margin: 0 auto;">
            <div style="text-align: center;margin: 30px;">
                <img src="{{$details['logo']}}" alt="logo" width="100">
            </div>
            <div style="background-color: #fff;border-radius: 5px;box-shadow: 0px 0px 15px 0px #9494941a;padding: 10px 30px;">
                <h3>Hi, {{$details['name']}}!</h3>
                <p style="margin: 8px 0 28px 0;">Please enter the code below to verify your email address.</p>
                <div>
                    <input type="text" id="code" readonly value="{{$details['code']}}" style="margin: auto;display: block;cursor: pointer;border: none;background-color: #2d3748;color: #fff;font-size: 16px;padding: 15px 25px;border-radius: 5px;width: 110px;outline: none;margin-bottom: 30px;text-align: center;position: relative;">
                    <span id="copy" style="background-color: #333;color: #fff;padding: 5px 10px;border-radius: 5px;position: absolute;left: 0;display: none;opacity: 0;left: 50%;transform: translate(-50%, -130px);">Code Copied</span>
                </div>
                <p style="margin: 20px 0 15px 0;">If you did not create an account, no further action is required.</p>
                <p>Regards,<br>{{$details['team']}}</p>
            </div>
            <p style="text-align:center;padding:30px 0;color:#555;font-size:15px">&copy; 2021 {{$details['app']}}. All rights reserved.</p>
        </div>

        <script>
            var code = document.getElementById('code');

            code.onclick = function () {
                this.select(true);
                document.execCommand("copy");
                document.getElementById('copy').style.display = 'inline';
                document.getElementById('copy').style.opacity = '.9';
                setTimeout(function(){
                	document.getElementById('copy').style.display = 'none';
                }, 1000);
            }
        </script>
    </body>
</html>
