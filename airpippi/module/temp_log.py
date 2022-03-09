from . import module
from flask import jsonify
import os
import json
import threading
import urllib.request
from datetime import datetime, timezone, timedelta
import time
from . import temp
import logging

logfile_path = "data/temp.json"

@module.route("/temp/log")
def temp_log(direct = False):
    if os.path.exists(logfile_path):
        with open(logfile_path, 'r') as f:
            logdata = json.loads(f.read())
        result = {"result": "ok", "data": logdata['data']}
    else:
        result = {"result": "logfile does not found"}
    return result if direct else jsonify(result)


def temp_logger():
    while True:
        time.sleep(10)
        try:
            # fetch latest temperature
            apidata = temp(direct = True)
            if apidata['result'] != 'ok':
                time.sleep(50)
                continue
        except:
            logging.exception("Airpippi[temp_log]: Failed to get latest temperature.")
            continue

        # load log file or create empty data
        if os.path.exists(logfile_path):
            with open(logfile_path, 'r') as f:
                logdata = json.loads(f.read())
        else:
            logdata = {"data": []}
        
        # create timezone data
        jp = timezone(timedelta(hours=9), 'Asia/Tokyo')
        now = datetime.now(tz=jp).isoformat(timespec = 'seconds')
        
        # insert data
        data = { "time": now, "temp": apidata['temperature'] }
        logdata['data'].insert(0, data)
        
        # drop old data
        if 60 < len(logdata['data']):
            logdata['data'].pop()
        
        # write data
        with open(logfile_path, 'w') as f:
            f.write(json.dumps(logdata))
        time.sleep(50)

temp_logger_thread = threading.Thread(target = temp_logger)
temp_logger_thread.setDaemon(True)
temp_logger_thread.start()

