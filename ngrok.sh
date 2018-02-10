#!/bin/bash

set -euxo pipefail

wget https://bin.equinox.io/c/4VmDzA7iaHb/ngrok-stable-linux-amd64.zip
unzip ngrok-stable-linux-amd64.zip
sudo mv ngrok /usr/local/bin/ngrok
ngrok authtoken $NGROK_AUTH_TOKEN
ngrok tcp 3306 --log=stdout > /dev/null &
sleep 1
NGROK_STATUS=$(curl http://localhost:4040/api/tunnels)
echo $NGROK_STATUS
sleep 300
exit 0
