<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>取引完了のお知らせ</title>
</head>
<body>
    <p>こんにちは、{{ $purchase->item->user->name }} 様。</p>

    <p>商品「{{ $itemName }}」が取引完了しました。</p>
    <p>購入者: {{ $buyerName }}様</p>
    <p>取引評価を行ってください。</p>
    <p>よろしくお願い致します。</p>
</body>
</html>