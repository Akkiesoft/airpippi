from flask import Flask
from module import *


app = Flask(__name__)
app.register_blueprint(module)

@app.route("/")
def root():
    return "{'name': 'Airpippi', version: '2.0'}"

if __name__ == "__main__":
    port = int(config['DEFAULT']['port']) if 'port' in config['DEFAULT'] else 8000
    app.run(port = port)
