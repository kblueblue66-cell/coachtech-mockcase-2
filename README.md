# coachtech-kintai

## 機能一覧

## 環境構築
**Dockerビルド**
1. `git clone　リポジトリURL`
2. cd coachtech-mockcase-2
3. DockerDesktopアプリを立ち上げる
4. `docker-compose up -d --build`

> *MacのM1・M2チップのPCの場合、`no matching manifest for linux/arm64/v8 in the manifest list entries`のメッセージが表示されビルドができないことがあります。
エラーが発生する場合は、docker-compose.ymlファイルの「mysql」内に「platform」の項目を追加で記載してください*
``` bash
mysql:
    platform: linux/x86_64(この文追加)
    image: mysql:8.0.26
    environment:
```
**Laravel環境構築**
1. `docker-compose exec php bash`
2. `composer install`
3. 「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.envファイルを作成
```bash
cp .env.example .env
```
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

6. マイグレーションの実行
``` bash
php artisan migrate
```

7. シーディングの実行
``` bash
php artisan db:seed
```
8. シンボリックリンク作成
``` bash
php artisan storage:link
```
9. テストケース
```bash
php artisan test
```

## メール認証
mailtrapというツールを使用しています。<br>
以下のリンクから会員登録をして下さい。<br>
https://mailtrap.io/

メールボックスのIntegrationsから 「laravel 7.x and 8.x」を選択し、　<br>
.envファイルのMAIL_MAILERからMAIL_ENCRYPTIONまでの項目をコピー＆ペーストしてください。　<br>
MAIL_FROM_ADDRESSは任意のメールアドレスを入力してください。　
```text
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=（MailtrapのUser Nameを入力）
MAIL_PASSWORD=（MailtrapのPasswordを入力）
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=(任意のメールアドレス)
MAIL_FROM_NAME="${APP_NAME}"
```

## テーブル仕様


## ER図

## テストアカウント
name:一般ユーザー
email:test@example.com
password:password123
-----------------------
name:管理者ユーザー
email:admin@example.com
password:adminpass888
-----------------------

## URL
- 開発環境：http://localhost/
- phpMyAdmin:：http://localhost:8080/

