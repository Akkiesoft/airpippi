from . import module
from flask import jsonify
import bme680

try:
    sensor = bme680.BME680(bme680.I2C_ADDR_PRIMARY)
except (RuntimeError, IOError):
    sensor = bme680.BME680(bme680.I2C_ADDR_SECONDARY)

sensor.set_humidity_oversample(bme680.OS_2X)
sensor.set_pressure_oversample(bme680.OS_4X)
sensor.set_temperature_oversample(bme680.OS_8X)
sensor.set_filter(bme680.FILTER_SIZE_3)
sensor.set_gas_status(bme680.ENABLE_GAS_MEAS)

@module.route("/temp")
def temp(direct = False):
    try:
        sensor.get_sensor_data()
        gas = "-"
        if sensor.data.heat_stable:
            gas = '{0},{1}'.format(output, sensor.data.gas_resistance)
        result = {
             "result": "ok",
             "driver": "bme680",
             "temperature": "%.2f" % sensor.data.temperature,
             "humidity": "%.2f" % sensor.data.humidity,
             "pressure": "%.2f" % sensor.data.pressure,
             "gas": gas
        }
    except:
        result = {"result": "failed", "driver": "bme680"}
    return result if direct else jsonify(result)
