● 当該APIチェッカーの使い方について

丹原 康博 Yasuhiro Tambara 2015.04.07

● このチェッカーに含まれている以下の2ファイルは、このAPIが正しくデータベースサーバーに問い合わせできているかどうか検証ツールです。
dbs.php
db.php

● このディレクトリ checkers は、実運用環境からは削除するようにしてください。

● dbs.php : 問い合わせ可能なデータベースファイル名のリストをブラウザのウィンドウ内にリストします。
・※公式ApiForPhp の ListDatabases() に相当します。
・URL引数に必ず host: を指定してください。
（例）http://127.0.0.1/siteroot/connect/checkers/dbs.php?host=127.0.0.1:8080

● db.php : データベースファイル名を指定してレイアウト名、スクリプト名をブラウザのウィンドウ内にリストします。
url引数として以下の4つの値が必須です。
host: 
db  : データベースファイル名
acc : アカウント名
pas : パスワード
・※公式ApiForPhp の ListScripts(),ListLayout() に相当します。
（例）http://127.0.0.1/siteroot/connect/checkers/db.php?host=127.0.0.1:8080&db=***&acc=***&pas=***

以上です。