# フリマアプリ

![image](/images/flea.png)

## ER 図

![image](/images/ER.png)

## 使用技術(実行環境)

-   Mysql8.0.36
-   PHP8.3.15
-   Laravel11.37.0
-   nginx1.27.2
-   jQuery3.6.3

## 環境構築

### Docker ビルド

1. `git@github.com:hiroseryo/flea-market.git`

2. DockerDesktop 　アプリの立ち上げ

3. `docker compose up -d --build`

### Laravel 環境構築

1. `docker compose exec app bash`

2. `cd laravel-project`

3. `composer install`

4. .env に以下の環境変数を追加

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```

5. アプリケーションの作成

```
php artisan key:generate
```

6. マイグレーションとシーディングの実行

```
php artisan migrate:fresh --seed
```

7. テスト用マイグレーションの実行

```
php artisan migrate --env=testing
```

## メール認証実装

Mailtrap を使用しての実装です。ログイン->Email Testing->SMTP の画面下の PHP/Laravel 9+ を選んで下さい。
そして発行された PASSWORD と USERNAME をクリックして.env に貼り付けて下さい。

```
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=yourusername
MAIL_PASSWORD=yourpassword
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Stripe 機能

Stripe 公式のサイトでログイン->開発者->API キーの画面に公開可能キーとシークレットキーをコピーして.env に貼り付けて下さい。

```
STRIPE_KEY=public key
STRIPE_SECRET=secret key
```

## URL

-   開発環境： http://localhost/8000
-   phpMyAdmin : http://localhost:8080/
