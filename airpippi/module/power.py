from . import module
from . import config
from flask import jsonify
from time import sleep
from gpiozero import LED

optocoupler = LED(config['power']['gpio'])

@module.route("/power", methods=['POST'])
def rungpio(direct = False):
    optocoupler.on()
    sleep(0.2)
    optocoupler.off()
    return {"result": "ok"} if direct else jsonify({"result": "ok"})