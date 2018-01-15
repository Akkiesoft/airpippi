#!/usr/bin/env python
# -*- coding: utf-8 -*-

import sys
import datetime
import json
from mastodon import Mastodon, StreamListener
import airpippi_cmd

# get consumer key
try:
  f = open('/opt/airpippi/mstdnapp.json', 'r')
  appData = json.load(f)
  if not "client_key" in appData \
     and not "client_secret" in appData:
      sys.exit()
except IOError:
  # file not exists.
  sys.exit()

def load_authconfig():
  try:
      f = open('/opt/airpippi/mstdnauth.json', 'r')
      jsonData = json.load(f)
      if not "airpippi_user" in jsonData:
        # not found "airpippi_mstdn" key.
        return -1
      return jsonData
  except IOError:
      # file not exists.
      return -1

jsonData = load_authconfig()
if jsonData < 0:
  sys.exit()
me = "@"+jsonData["airpippi_user"]

# connect to mastodon
mstdn = Mastodon(
    client_id = appData["client_key"],
    client_secret = appData["client_secret"],
    access_token = jsonData["access_token"],
    api_base_url = appData["url"]
)

class MaStreamListener(StreamListener):
  def on_update(self, status):
    try:
      # pass if toot from airpippi
      if status['application'] != None and status['application']['name'] == u"エアぴっぴ":
        return True
      # if not mention in tweet, pass
      if not me in status['content']:
        return True
      if status['mentions'].count == 0:
        return True
      # get sn
      sn = status['account']['acct']
      # reload config
      jsonData = load_authconfig()
      if jsonData < 0:
        sys.exit()
      # check allowed user
      if "accounts" in jsonData:
        if not sn in jsonData["accounts"]:
          return True
      else:
        if not sn in jsonData["airpippi_user"]:
          return True

      now = datetime.datetime.today().strftime(" (%Y/%m/%d %H:%M:%S)")
      result = airpippi_cmd.run(status['content'])
      for i in result:
        mstdn.status_post(
          status = "@" + sn + " " + i + now,
          in_reply_to_id = status['id'],
          visibility = "direct"
        )

    except KeyError:
      pass

try:
  mstdn.stream_user(MaStreamListener(), async=False)
except(KeyboardInterrupt):
  print("exit.")
