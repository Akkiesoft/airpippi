#!/usr/bin/env python
# -*- coding: utf-8 -*-

# Airpippi twitter crawler
#  2015 Akkiesoft / Eject-Command-Users-Group
# Inspire From:
#   http://peter-hoffmann.com/2012/simple-twitter-streaming-api-access-with-python-and-oauth.html

import sys
import datetime
import json
import tweepy
import airpippi_cmd

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

def load_authconfig():
	try:
		f = open('/opt/airpippi/twitterauth.json', 'r')
		jsonData = json.load(f)
		if not "airpippi_twit" in jsonData:
			# not found "airpippi_twit" key.
			return -1
		return jsonData
	except IOError:
		# file not exists.
		return -1

jsonData = load_authconfig()
if jsonData < 0:
	sys.exit()
me = "@"+jsonData["airpippi_twit"]

# connect to twitter
auth = tweepy.OAuthHandler(consumer_key, consumer_secret)
auth.set_access_token(jsonData["access_token"], jsonData["access_secret"])
api = tweepy.API(auth)

# define class
class CustomStreamListener(tweepy.StreamListener):
	def on_status(self, status):
		# pass if tweet from airpippi
		if status.source == u"エアぴっぴ":
			return True
		# if not mention in tweet, pass
		if not me in status.text:
			return True
		# get sn
		sn = status.author.screen_name
		# reload config
		jsonData = load_authconfig()
		if jsonData < 0:
			sys.exit()
		# check allowed user
		if "accounts" in jsonData:
			# 大文字小文字の個別はtweepyが厳しく見ているっぽいので無駄だった
			if not sn in jsonData["accounts"]:
				return True
		else:
			if not sn in jsonData["airpippi_twit"]:
				return True

		now = datetime.datetime.today().strftime(" (%Y/%m/%d %H:%M:%S)")
		result = airpippi_cmd.run(status.text)
		for i in result:
			api.update_status(
				status = "@" + sn + " " + i + now,
				in_reply_to_status_id = status.id
			)

	def on_error(self, status_code):
		print >> sys.stderr, 'Airpippi: Encountered error with status code:', status_code
		return True # Don't kill the stream

	def on_timeout(self):
		print >> sys.stderr, 'Airpippi: Timeout...'
		return True # Don't kill the stream

# start user stream
sapi = tweepy.streaming.Stream(auth, CustomStreamListener())
sapi.userstream()
