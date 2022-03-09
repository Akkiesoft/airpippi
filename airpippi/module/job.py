from . import module
from . import run_command
from flask import jsonify, request, Response
import os
import json
from uuid import uuid4
import threading
import time

joblist_path = "config/joblist.json"

def job_list_update(data):
    global jobdata
    if type(data) is dict:
        jobdata = data_for_runner(data)
        data = json.dumps(data, indent=4, ensure_ascii=False)
    else:
        jobdata = data_for_runner(json.loads(data))
    with open(joblist_path, 'w') as f:
        f.write(data)
    return

def check_old_joblist(data):
    changed = False
    j = json.loads(data)
    if type(j) is list:
        new_j = {}
        for i in j:
            new_j[str(uuid4())] = i
        data = json.dumps(new_j, indent=4, ensure_ascii=False)
        job_list_update(data)
    return data

def job_list_get(json_decode = False):
    if not os.path.isfile(joblist_path):
        data = '{}'
        job_list_update(data)
    else:
        with open(joblist_path) as f:
            data = f.read()
    data = check_old_joblist(data)
    if json_decode:
        return json.loads(data)
    else:
        return Response(data, mimetype='Application/json')

@module.route("/job/list")
def job_list():
    return job_list_get()

@module.route("/job/add", methods=['POST'])
def job_add():
    data = job_list_get(json_decode = True)

    return ""

@module.route("/job/delete", methods=['POST'])
def job_delete():
    jobid = request.form['id'] if 'id' in request.form else -1
    if (jobid == -1):
        return jsonify({"result": "id is required", "code": 1}), 400
    data = job_list_get(json_decode = True)
    removed = data.pop(jobid)
    job_list_update(data)
    return jsonify({"result": "ok", "code": 0, "jobname": removed['name'], "jobid": jobid})

@module.route("/job/enabled", methods=['POST'])
def job_enabled():
    jobid = request.form['id'] if 'id' in request.form else -1
    if (jobid == -1):
        return jsonify({"result": "id is required", "code": 1}), 400
    enabled = int(request.form['enabled']) if 'enabled' in request.form else -1
    if (enabled != 0 and enabled != 1):
        return jsonify({"result": "enabled parameter must 0 or 1", "code": 1}), 400
    data = job_list_get(json_decode = True)
    data[jobid]['enabled'] = bool(enabled)
    job_list_update(data)
    return jsonify({"result": "ok", "code": 0, "jobname": data[jobid]['name'], "jobid": jobid, "enabled": data[jobid]['enabled']})

def data_for_runner(d):
    result = []
    for i in d.values():
        result.append(i)
    return result

def job_runner():
    global jobdata
    wl = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun']
    # minute
    m = time.localtime()[4]
    while True:
        time.sleep(1)
        now = time.localtime()
        if m == now[4]:
            continue
        h = now[3]
        m = now[4]
        w = wl[now[6]]
        for i in jobdata:
            if i['hour'] == h and i['min'] == m and i['dow'][w] and i['enabled']:
                run_command(taskid = i['task'])

jobdata = data_for_runner(job_list_get(json_decode = True))
job_runner_thread = threading.Thread(target = job_runner)
job_runner_thread.setDaemon(True)
job_runner_thread.start()