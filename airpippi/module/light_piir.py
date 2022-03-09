from . import module
from . import config
from flask import jsonify
import piir

tx = config['light']['ir_tx']
light = piir.Remote('config/light.json', tx)

@module.route("/light/power", methods=['POST'])
def light_power(direct = False):
    light.send('power')
    result = {"result": "ok"}
    return result if direct else jsonify(result)

@module.route("/light/bright", methods=['POST'])
def light_bright():
    light.send('bright')
    return jsonify({"result": "ok"})

@module.route("/light/dark", methods=['POST'])
def light_dark():
    light.send('dark')
    return jsonify({"result": "ok"})

@module.route("/light/warm", methods=['POST'])
def light_warm():
    light.send('warm')
    return jsonify({"result": "ok"})

@module.route("/light/cold", methods=['POST'])
def light_cold():
    light.send('cold')
    return jsonify({"result": "ok"})
