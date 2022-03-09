from . import module
from . import config
from flask import jsonify
import subprocess

@module.route("/light/power", methods=['POST'])
def light_power(direct = False):
    subprocess.run('/usr/bin/ir-ctl -s /opt/airpippi/config/R5.0-DL/power', shell=True)
    result = {"result": "ok"}
    return result if direct else jsonify(result)

@module.route("/light/bright", methods=['POST'])
def light_bright():
    subprocess.run('/usr/bin/ir-ctl -s /opt/airpippi/config/R5.0-DL/bright', shell=True)
    return jsonify({"result": "ok"})

@module.route("/light/dark", methods=['POST'])
def light_dark():
    subprocess.run('/usr/bin/ir-ctl -s /opt/airpippi/config/R5.0-DL/dark', shell=True)
    return jsonify({"result": "ok"})

@module.route("/light/warm", methods=['POST'])
def light_warm():
    subprocess.run('/usr/bin/ir-ctl -s /opt/airpippi/config/R5.0-DL/warm', shell=True)
    return jsonify({"result": "ok"})

@module.route("/light/cold", methods=['POST'])
def light_cold():
    subprocess.run('/usr/bin/ir-ctl -s /opt/airpippi/config/R5.0-DL/cold', shell=True)
    return jsonify({"result": "ok"})
