from . import module
from . import config
import threading
import time
from . import temp
import logging
from zabbix_utils import ItemValue, Sender

def temp_zabbix():
    while True:
        try:
            # fetch latest temperature
            apidata = temp(direct = True)
            if apidata['result'] != 'ok':
                time.sleep(60)
                continue
        except:
            logging.exception("Airpippi[temp_zabbix]: Failed to get latest temperature.")
            continue

        # insert data
        temp_driver = config['DEFAULT']['TemperatureDriver']
        hostname = config['zabbix']['hostname'] if 'hostname' in config['zabbix'] else ""
        if hostname == "":
            logging.exception("Airpippi[temp_zabbix]: Zabbix hostname is not set.")
            break

        params = [ ItemValue(hostname, "room_temp", apidata['temperature']) ]
        if temp_driver == "bme680" or temp_driver == "scd41":
            params.append(ItemValue(hostname, "room_humidity", apidata['humidity']))
        if temp_driver == "bme680":
            params.append(ItemValue(hostname, "room_pressure", apidata['pressure']))
        if temp_driver == "scd41":
            params.append(ItemValue(hostname, "room_co2", apidata['co2']))

        try:
            result = Sender(use_config=True).send(params)
        except:
            logging.exception("Airpippi[temp_zabbix]: Failed to send temperature to zabbix.")
        time.sleep(60)

temp_zabbix_thread = threading.Thread(target = temp_zabbix)
temp_zabbix_thread.setDaemon(True)
temp_zabbix_thread.start()

