#!/bin/bash

set -euxo pipefail

wget https://bin.equinox.io/c/4VmDzA7iaHb/ngrok-stable-linux-amd64.zip
unzip ngrok-stable-linux-amd64.zip
sudo mv ngrok /usr/local/bin/ngrok
ngrok authtoken $NGROK_AUTH_TOKEN
ngrok tcp 3306 --log=stdout > /dev/null &
