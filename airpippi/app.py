from flask import Flask
from module import *


app = Flask(__name__, static_folder='static', static_url_path='')
app.register_blueprint(module)

@app.route("/")
def root():
    return app.send_static_file('index.html')

@app.route("/api")
def api_root():
    return "{'name': 'Airpippi', version: '2.0'}"

if __name__ == "__main__":
    port = int(config['DEFAULT']['port']) if 'port' in config['DEFAULT'] else 8000
    app.run(port = port)
