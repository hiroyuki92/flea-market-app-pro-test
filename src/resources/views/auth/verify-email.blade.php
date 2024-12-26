<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>メールアドレスの確認</title>
</head>
<body style="font-family: sans-serif;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #333;">メールアドレスの確認</h2>
        
        <p style="margin: 20px 0; line-height: 1.6;">
            ご登録ありがとうございます。<br>
            以下のボタンをクリックして、メールアドレスの確認を完了してください。
        </p>

        <div style="margin: 30px 0; text-align: center;">
            <a href="{{ $url }}" 
               style="background-color: #4CAF50; 
                      color: white; 
                      padding: 12px 24px; 
                      text-decoration: none; 
                      border-radius: 4px;
                      display: inline-block;">
                メールアドレスを確認する
            </a>
        </div>

        <p style="margin: 20px 0; line-height: 1.6; font-size: 14px; color: #666;">
            このリンクの有効期限は60分です。<br>
            このメールに心当たりがない場合は、お手数ですが破棄してください。
        </p>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #666;">
            <p>
                ボタンがクリックできない場合は、以下のURLをコピーしてブラウザに貼り付けてください：<br>
                {{ $url }}
            </p>
        </div>
    </div>
</body>
</html>