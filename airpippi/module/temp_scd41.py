from . import module
from flask import jsonify
from scd4x import SCD4X

sensor = SCD4X(quiet=False)
sensor.start_periodic_measurement()

@module.route("/temp")
def temp(direct = False):
    try:
        co2, temperature, relative_humidity, timestamp = sensor.measure()
        result = {
             "result": "ok",
             "driver": "scd41",
             "temperature": "%.2f" % temperature,
             "humidity": "%.2f" % relative_humidity,
             "co2": "%.2f" % co2
        }
    except:
        result = {"result": "failed", "driver": "scd41"}
    return result if direct else jsonify(result)
