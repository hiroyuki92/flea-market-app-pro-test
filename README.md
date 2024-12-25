# coachtechフリマ

## 環境構築
**Dockerビルド**
1. `git clone git@github.com:hiroyuki92/flea-market-app.git`
2. `cd flea-market-app`     クローンしたディレクトリに移動する
3. DockerDesktopアプリを立ち上げる
4. `docker-compose up -d --build`

**Laravel環境構築**
1. `docker-compose exec php bash`
2. `composer install`
3. 「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.envファイルを作成
4. .envに以下の環境変数を追加
``` text
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
5. アプリケーションキーの作成
``` bash
php artisan key:generate
```

6. マイグレーションとシーディングの実行
``` bash
php artisan migrate --seed
```  
　実行すると以下の初期データが作成されます  
  - 管理者ユーザー  
	メールアドレス: admin@example.com  
	パスワード: password  
	権限: 管理者  
  - 一般ユーザー  
	メールアドレス: user@example.com  
	パスワード: password  
	権限: 一般ユーザー  
  - ランダムな一般ユーザー  
	ダミーユーザーが10人生成されます。  
	メールアドレスや名前はランダムに設定されています。  
	パスワード: password  

7. ストレージリンクの作成
``` bash
php artisan storage:link
```
8. Stripe決済機能の設定 
 
 	⚫︎Stripeアカウントの準備
  	 - [Stripe Dashboard](https://dashboard.stripe.com/register)でアカウントを作成
  	 - テストモードになっていることを確認（右上のスイッチで切り替え可能）
  	 - Developers > API keysセクションからAPIキーを取得:
  	   - シークレットキー（sk_test_...）
   	   - 公開可能キー（pk_test_...）

 	⚫︎テスト環境用の環境変数設定
   	- `.env`と`.env.testing`ファイルに以下を追加：
 	  ```env
   		STRIPE_SECRET=sk_test_あなたのシークレットキー
   		STRIPE_KEY=pk_test_あなたの公開可能キー
   		```

	#### Stripe決済機能の設定の注意事項
	- シークレットキーは絶対に公開リポジトリにコミットしないでください

9. テストの実行
``` bash
php artisan test
```

## 使用技術(実行環境)
- PHP8.3.0
- Laravel8.83.27
- MySQL8.0.26
- nginx 1.21.1
- Stripe決済システム

## ER図
```mermaid
erDiagram
    USERS {
        bigint id PK 
        varchar(255) name 
        varchar(255) email UK
        varchar(255) password 
        varchar(255) profile_image
	varchar(10) postal_code 
        varchar(255) address_line 
        varchar(255) building
	varchar(255) role
        datetime created_at 
        datetime updated_at
　　　　　boolean first_login  
    }

    ITEMS {
        bigint id PK 
        bigint user_id FK
        VARCHAR(255) name 
        VARCHAR(255) brand 
        decimal price 
        text description 
        VARCHAR(255) image_url
	int condition 
        datetime created_at 
        datetime updated_at
	boolean sold_out 
    }
    
    CATEGORIES {
        bigint id PK 
        varchar(255) name UK
        datetime created_at 
        datetime updated_at 
 　　}

    CATEGORY_ITEM {
        bigint id PK
	bigint item_id FK
	bigint category_id FK 
        datetime created_at 
        datetime updated_at 
 　　}

    PURCHASES{
        bigint id PK
        bigint user_id FK
        bigint item_id FK
        varchar(10) shipping_postal_code 
        varchar(255) shipping_address_line 
        varchar(255) shipping_building
        int　payment_method　
        datetime created_at 
        datetime updated_at
    }

    COMMENTS {
        bigint id PK 
        bigint user_id FK
        bigint item_id FK
        text content 
        datetime created_at 
        datetime updated_at 
    }

    FAVORITES {
        bigint id PK 
        bigint user_id FK
        bigint item_id FK
        datetime created_at 
        datetime updated_at 
    }

    %% リレーションシップの定義
    USERS ||--o{ PURCHASES : ""
    USERS ||--o{ ITEMS : ""
    USERS ||--o{ COMMENTS : ""
    USERS ||--o{ FAVORITES : ""
    CATEGORIES ||--o{ CATEGORY_ITEM : ""
    ITEMS ||--o{ CATEGORY_ITEM : ""
    ITEMS ||--o{ COMMENTS : ""
    ITEMS ||--o{ FAVORITES : ""
    ITEMS ||--o| PURCHASES : ""

```


## URL
- 開発環境：http://localhost/
- phpMyAdmin:：http://localhost:8080/
