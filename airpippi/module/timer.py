from . import module
from . import run_command
from flask import jsonify, request
import threading
import time

@module.route("/timer", methods=['POST'])
def timer(min_arg = None):
    minutes = request.form.get('time', type=int) if min_arg is None else min_arg
    if (minutes < 1):
        result = {"result": "time is required and must be an integer greater than 0", "code": 1}
    else:
        timer_time = int(time.time()) + (minutes * 60)
        timer_list.append({'time': timer_time, 'task': 0})
        readable_time = time.strftime("%Y/%m/%d %H:%M:%S", time.localtime(timer_time))
        result = {"result": readable_time, "time": minutes}
    return jsonify(result) if min_arg is None else result

def timer_runner():
    global timer_list
    while True:
        time.sleep(1)
        now = time.time()
        if not len(timer_list):
            continue
        for i in timer_list[:]:
            if i['time'] < now:
                run_command(taskid = i['task'])
                timer_list.remove(i)

timer_list = []
timer_runner_thread = threading.Thread(target = timer_runner)
timer_runner_thread.setDaemon(True)
timer_runner_thread.start()