# エアぴっぴ

## なにこれ

Raspberry Piにエアコンのリモート操作をしてもらうためのソフトウェアです。

元々はEjectコマンドユーザー会のリポジトリに置いていたものを、もう少しまじめに作り替えたものです。

## できること

+ 改造済み学習リモコンとフォトカプラを用いた電源ボタンの間接的制御機能
+ DS18B20+を用いた室温の記録(いまのところ60分間記録で固定)
+ ローカルWeb画面からの各種制御
+ Twitter連携による遠隔制御

## 必要なもの(ハードウェア)

### Raspberry Pi

Model Aでも、Model Bでも、どのRaspberry Piでも良いです。Raspberry Pi 2でももちろん良いですが、性能的に持て余す感じです。

### 改造済み学習リモコン

作り方は「Raspberry Pi〔実用〕入門 ~手のひらサイズのARM/Linuxコンピュータを満喫!」( http://www.amazon.co.jp/dp/4774158550 )の「Appendix B:Ejectコマンドで遊ぼう」-「B-4:Ejectを卒業しよう」に書いたのでそれを見てください。

接続は、3V,GND,GPIO4です。

### DS18B20+温度センサー

https://learn.adafruit.com/adafruits-raspberry-pi-lesson-11-ds18b20-temperature-sensing を参照してください。

接続は、GPIO#17とGNDです。

## 必要なもの(ソフトウェア)

### Ansibleがインストールされたマシン

インストールにはAnsibleを使用します。インストール方法は後述します。

### Raspbian

Raspberry PiのOSはRaspbianを前提としています。SDカードのサイズはRaspbianの要件に準じます。

また、後述のAnsibleによるインストールのため、あらかじめSSH公開鍵を登録してください。

### WebIOPi

WebIOPi 0.7.1のみ、別途用意をしてください。

https://code.google.com/p/webiopi/

roles/common/files/WebIOPi-0.7.1.tar.gzとして配置します。

 ```
 ※現状では、後述のAnsibleでインストールを実行しても失敗します。失敗た後に、インストール先のホームディレクトリに展開されたWebIOPiを手動でインストールしてください。
 
 $ sudo ./setup.sh
 ```

## インストール

Ansibleを使用しています。

host_varsに変数ファイル用意します。exampleをコピーして、実行先のホスト名に合わせてください。設定の詳細は以下のとおりです。

なお、twitterのアプリケーションのキーは各自で取得してください。

 ```
 ---
 ds18b20_id: <DS18B20のID(28-000001234567のように記述)>
 
 tw_consumer_key: <Twitterのアプリのコンシューマーキー>
 tw_consumer_secret: <Twitterのアプリのコンシューマーシークレットキー>
 ```

Playbookの実行は以下のとおりです。

 ```
 $ ansible-playbook -i production -u pi site.yml
 ```

Playbookの実行後は、センサーを有効にするために再起動が必要です。

## Copyright

The MIT License (MIT)

Copyright (c) 2015 Akira Ouchi \<akkiesoft -at- marokun.net\> a.k.a. [@Akkiesoft](https://www.twitter.com/Akkiesoft)

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
