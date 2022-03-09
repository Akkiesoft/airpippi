from . import module
from . import config
from flask import jsonify
from w1thermsensor import W1ThermSensor

temp_enabled = False
try:
    if 'Device' in config['ds18b20']:
        ds18b20 = W1ThermSensor(sensor_id = config['ds18b20']['Device'])
    else:
        ds18b20 = W1ThermSensor()
    temp_enabled = True
except:
    pass

@module.route("/temp")
def temp(direct = False):
    if temp_enabled:
        result = {"result": "ok", "driver": "ds18b20", "temperature": "%.2f" % ds18b20.get_temperature()}
    else:
        result = {"result": "initialize failed", "driver": "ds18b20"}
    return result if direct else jsonify(result)