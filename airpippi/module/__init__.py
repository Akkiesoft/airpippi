import os
import configparser
from flask import Blueprint, current_app

module = Blueprint('module', __name__)

def run_command(text = '', taskid = -1):
    result = []
    try:
        if "電源" in text or taskid == 0:
            rungpio(direct = True)
            result.append("電源を操作しました。")

        if "室温" in text:
            temp = temp_log(direct = True)["data"][0]["temp"]
            temp = "わからない" if float(temp) < 0 else temp + "℃"
            result.append("今の室温は" + temp + "です。")

        if "タイマー" in text:
            msg = ""
            minutes = getTimer(text)
            if minutes < 0:
                msg = "何分後にタイマー実行するか指定してください。"
            else:
                timer(minutes)
                msg = str(minutes) + "分後くらいにタイマー実行します。"
            result.append(msg)

        if "照明オフ" in text or taskid == 1:
            time.sleep(2)
            light_power(direct = True);
            time.sleep(2)
            light_power(direct = True);
            msg = "照明をオフにしました。"
            result.append(msg)

        if "照明オン" in text or taskid == 2:
            time.sleep(2)
            light_power(direct = True);
            msg = "照明をオンにしました。"
            result.append(msg)
    except:
        print("[run_command] Unexpected error:", sys.exc_info()[0])
    return result

def config_reload():
    global config
    config.read("config/airpippi.conf")

def config_update():
    global config
    with open("config/airpippi.conf", 'w') as f:
        config.write(f)

config = configparser.ConfigParser()
config_reload()

from .power import *

temp_driver = False
if 'TemperatureDriver' in config['DEFAULT']:
    if config['DEFAULT']['TemperatureDriver'] == 'ds18b20':
        temp_driver = True
        from .temp_ds18b20 import *
    elif config['DEFAULT']['TemperatureDriver'] == 'bme680':
        temp_driver = True
        from .temp_bme680 import *
    elif config['DEFAULT']['TemperatureDriver'] == 'scd41':
        temp_driver = True
        from .temp_scd41 import *
else:
    @module.route("/temp")
    def temp():
        return jsonify({"driver": "", "result": "TemperatureDriver parameter is invalid or not found."})

if temp_driver:
    from .temp_log import *
    if 'zabbix' in config:
        from .temp_zabbix import *

from .timer import *
from .job import *
from .light import *

if 'mastodon' in config:
    from .mastodon import *
