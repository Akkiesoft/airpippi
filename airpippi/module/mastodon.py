from . import module
from . import config, config_reload, config_update, run_command
from flask import jsonify, request
import datetime
import threading
from mastodon import Mastodon, StreamListener
import re
import sys
import time
import traceback

def get_allowed_users():
    # reload config
    config_reload()
    if not 'allowed_users' in config['mastodon']:
        return []
    return config['mastodon']['allowed_users'].replace(' ', '').split(',')

def getTimer(text):
    search_time = re.search(u"([0-9]+)分", text)
    if search_time != None:
        return int(search_time.group(1))
    return -1;

class MaStreamListener(StreamListener):
    global mstdn, me
    def on_notification(self, status):
        try:
            toot = status['status']
            # pass if toot from airpippi
            if 'application' in toot and toot['application']:
               if 'name' in toot['application'] and toot['application']['name'] == "エアぴっぴ":
                   return True
            # if not mention in tweet, pass
            if not me in toot['content']:
                return True
            if toot['mentions'].count == 0:
                return True
            # get sn
            sn = status['account']['acct']
            # check allowed user
            users = get_allowed_users()
            if not sn in users:
                return True

            now = datetime.datetime.today().strftime(" (%Y/%m/%d %H:%M:%S)")
            result = run_command(text = toot['content'])
            for i in result:
                mstdn.status_post(
                    status = "@" + sn + " " + i + now,
                    in_reply_to_id = toot['id'],
                    visibility = "direct"
                )
        except:
            print("[mastodon.on_notification] Unexpected error:", sys.exc_info()[0])
            print(traceback.format_exc())

def mastodon_crawler():
    global mstdn, me, mastodon_enabled, mastodon_status, mastodon_config
    try:
        # connect to mastodon
        mstdn = Mastodon(
            client_id = config['mastodon']['client_key'],
            client_secret = config['mastodon']['client_secret'],
            access_token = config['mastodon']['access_token'],
            api_base_url = config['mastodon']['domain']
        )
    except:
        mastodon_status = "failed to connect to the mastodon server"

    try:
        me = mstdn.me()['acct']
        mastodon_enabled = True
        mastodon_status = "mastodon crawler started"
        mastodon_config = {
            'account': me,
            'domain': config['mastodon']['domain'],
            'allowed_users': get_allowed_users()
        }
        mstdn.stream_user(MaStreamListener(), run_async=False, reconnect_async=True, reconnect_async_wait_sec=60)
    except:
        mastodon_status = "failed to start streaming"

@module.route('/mastodon/status')
def mastodon_status():
    global me, mastodon_enabled, mastodon_status, mastodon_config
    mastodon_config = {
        'account': me,
        'domain': config['mastodon']['domain'],
        'allowed_users': get_allowed_users()
    }
    return jsonify({"enabled": mastodon_enabled, "status": mastodon_status, "config": mastodon_config })

@module.route('/mastodon/allowed_users', methods=['POST'])
def mastodon_update_allowed_users():
    global config
    users = request.form.get('users', type=str)
    config['mastodon']['allowed_users'] = users
    config_update()
    return jsonify({"result": "ok", "allowed_users": users })

mastodon_enabled = False
mastodon_config = {}
mastodon_status = ""
if ('client_key' in config['mastodon']
        and 'client_secret' in config['mastodon']
        and 'access_token' in config['mastodon']
        and 'domain' in config['mastodon']):
    mastodon_crawler_thread = threading.Thread(target = mastodon_crawler)
    mastodon_crawler_thread.setDaemon(True)
    mastodon_crawler_thread.start()
else:
    mastodon_status = "configure is not found or not enough"