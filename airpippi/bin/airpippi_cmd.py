#!/usr/bin/env python3
# -*- coding: utf-8 -*-

# Airpippi crawler commands

import os
import re
import json
import datetime

def getTemp():
	try:
		f = open('/opt/airpippi/temp.json', 'r')
		tempData = json.load(f)
		if "data" in tempData and "temp" in tempData["data"][0]:
			return tempData["data"][0]["temp"]
	except IOError:
		# file not exists.
		return -1
	except ValueError:
		return -1

def getTimer(text):
	search_time = re.search(u"([0-9]+)分", text)
	if search_time != None:
		return search_time.group(1)
	return -1;


def run(text):
	result = []

	if u"電源" in text:
		os.system("/usr/bin/php /opt/airpippi/bin/rungpio.php rungpio")
		result.append(u"電源を操作しました。")

	if u"室温" in text:
		temp = getTemp()
		temp = u"わからない" if temp < 0 else str(temp) + u"℃"
		result.append(u"今の室温は" + temp + u"です。")

	if u"タイマー" in text:
		msg = ""
		timer = getTimer(text)
		if timer < 0:
			msg = u"何分後にタイマー実行するか指定してください。"
		else:
			os.system("echo '/usr/bin/php /opt/airpippi/bin/rungpio.php rungpio' | /usr/bin/at now +" + timer + "minute");
			msg = timer + u"分後くらいにタイマー実行します。"
		result.append(msg)

	return result

# JSONを読み込む
def tw_load_authconfig():
	try:
		f = open('/opt/airpippi/twitterauth.json', 'r')
		jsonData = json.load(f)
		if not "airpippi_twit" in jsonData:
			# not found "airpippi_twit" key.
			return -1
		return jsonData
	except ValueError:
		# file not exists.
		return -1
	except IOError:
		# file not exists.
		return -1

# ツイートをチェックしてコマンド実行してリプライするとこまで
def tw_check(config, api, status):
	me = "@"+config["airpippi_twit"]

	# pass if tweet from airpippi
	if status.source == u"エアぴっぴ":
		return True
	# if not mention in tweet, pass
	if not me in status.text:
		return True
	# get sn
	sn = status.author.screen_name

	# check allowed user
	if "accounts" in config:
		# 大文字小文字の個別はtweepyが厳しく見ているっぽいので無駄だった
		if not sn in config["accounts"]:
			return True
	else:
		if not sn in config["airpippi_twit"]:
			return True

	now = datetime.datetime.today().strftime(" (%Y/%m/%d %H:%M:%S)")
	result = run(status.text)
	for i in result:
		api.update_status(
			status = "@" + sn + " " + i + now,
			in_reply_to_status_id = status.id
		)
	return

