# エアぴっぴ v2

## なにこれ

Raspberry Piにエアコンのリモート操作をしてもらうためのソフトウェアです。

元々はEjectコマンドユーザー会のリポジトリに置いていたものを、もう少しまじめに作り替えたものです。

## できること

+ 改造済み学習リモコンとフォトカプラを用いた電源ボタンの間接的制御機能
+ BME680/688もしくはDS18B20+を用いた室温の記録(いまのところ60分間記録で固定)
+ ローカルWeb画面からの各種制御
+ Mastodon連携による遠隔制御

## 必要なもの(ハードウェア)

### Raspberry Pi

Raspberry Pi Zero, 1〜4まで、どの世代でも良いです。

### 改造済み学習リモコン

作り方は「Raspberry Pi〔実用〕入門 ~手のひらサイズのARM/Linuxコンピュータを満喫!」( http://www.amazon.co.jp/dp/4774158550 )の「Appendix B:Ejectコマンドで遊ぼう」-「B-4:Ejectを卒業しよう」に書いたのでそれを見てください。

### DS18B20+温度センサー

https://learn.adafruit.com/adafruits-raspberry-pi-lesson-11-ds18b20-temperature-sensing を参照してください。

## 必要なもの(ソフトウェア)

### Ansibleがインストールされたマシン

インストールにはAnsible 2.9以上を使用します。インストール方法は後述します。

### Raspberry Pi OS

Raspberry Pi OS Buster以降(32 bit)を前提としています。SDカードのサイズはRaspberry Pi OSの要件に準じます。

また、後述のAnsibleによるインストールのため、あらかじめSSH公開鍵を登録してください。

## インストール

Ansibleを使用しています。

host_varsに変数ファイル用意します。exampleをコピーして、実行先のホスト名に合わせてください。

Playbookの実行例は以下のとおりです。

```
$ ansible-playbook -i inventory/myenv airpippi.yml
```

DS18B20+センサーを使用している場合のみ、Playbookの実行後は、センサーを有効にするためにOSの再起動が必要です。

## Copyright

The MIT License (MIT)

Copyright (c) 2015,2017,2022 Akira Ouchi \<akkiesoft -at- marokun.net\> a.k.a. [@Akkiesoft](https://social.mikutter.hachune.net/@akkiesoft)

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

### ライブラリなど

* bootstrap
    * Copyright (c) 2011-2021 Twitter, Inc
    * MIT License(see https://github.com/twbs/bootstrap/blob/master/LICENSE )
* Chart.js
    *Copyright (c) 2013-2021 Nick Downie
    * MIT License(see https://github.com/chartjs/Chart.js/blob/master/LICENSE.md )
