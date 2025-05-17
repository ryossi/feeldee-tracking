# Feeldee Tracking

feeldee-trackingは、[Feeldee Framework](https://github.com/ryossi/feeldee-framework)にアクセスカウンター、コンテンツ閲覧履歴などの追跡システムを追加するためのLaravelパッケージです。

## 利用者

### 導入方法

1. `composer require ryossi/feeldee-tracking`でパッケージを追加します。 
2. `php artisan migrate`でテーブルを作成します。
4. （オプション）`php artisan vendor:publish`を実行するとconfig/tracking.phpのサンプルが配置されます。

### 使用方法

具体的な機能については、[wiki](https://github.com/ryossi/feeldee-tracking/wiki)を参照してください。

## 開発者

### 導入方法

1. `git clone ryossi/feeldee-tracking`でパッケージをダウンロードします。 
2. `composer install`でPHPの依存パッケージをインストールします。

### テスト環境

通常のテストは、コマンドプロンプトで以下のコマンドを実行してください。

`./vendor/bin/phpunit --testsuite Feature`

### XDebug利用

1. `cp .env.example .env`で.envをコピーして設定をカスタマイズしてください。
2. `docker compose up -d`でテストコンテナを起動してください。
3. `docker exec -it feeldee-framework bash`でテストコンテナに入ります。
4. ソースコードの必要な部分にブレイクポイントを設定します。
5. テストコンテナのコマンドプロンプトで`./vendor/bin/phpunit --testsuite Feature`を実行してください。
6. 最後に`docker compose down`でテストコンテナを終了します。

## ライセンス

このプラグインは、[MIT licence.](https://opensource.org/licenses/MIT)のもとで公開されています。

## 参考

- テスト環境には、[Testbench](https://github.com/orchestral/testbench)を利用しています。
