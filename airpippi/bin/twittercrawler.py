#!/usr/bin/env python
# -*- coding: utf-8 -*-

# Airpippi twitter crawler
#  2015 Akkiesoft / Eject-Command-Users-Group
# Inspire From:
#   http://peter-hoffmann.com/2012/simple-twitter-streaming-api-access-with-python-and-oauth.html

import sys
import json
import tweepy
import airpippi_cmd

# get config
config = airpippi_cmd.tw_load_authconfig()
if config < 0:
	sys.exit()
me = "@"+config["airpippi_twit"]

# get consumer key
try:
	f = open('/opt/airpippi/twitterapp.json', 'r')
	appData = json.load(f)
	if not "consumer_key" in appData \
	   and not "consumer_secret" in appData:
		sys.exit()
	consumer_key    = appData["consumer_key"];
	consumer_secret = appData["consumer_secret"];
except IOError:
	# file not exists.
	sys.exit()

# connect to twitter
auth = tweepy.OAuthHandler(consumer_key, consumer_secret)
auth.set_access_token(config["access_token"], config["access_secret"])
api = tweepy.API(auth)

# 最後にチェックしたツイートIDをファイルから読む
try:
	f = open("/opt/airpippi/twitter_last_id.dat", 'r')
	since = f.read()
	f.close()
except IOError:
	since = ""
if since and since > 0:
	# 最後にチェックしたidがある
	tl = api.mentions_timeline(since_id = since)
else:
	# なんもないのでとりあえずデフォルト値で取る
	tl = api.mentions_timeline()

# 古いツイートから読んでいく
for status in tl[::-1]:
  airpippi_cmd.tw_check(config, api, status)

# 最後にチェックしたツイートIDを更新
if len(tl) > 0:
  f = open("/opt/airpippi/twitter_last_id.dat", 'w')
  f.write(str(tl[0].id))
  f.close()

