#!/bin/bash

ssh -tt -i "catch_me.pem" ubuntu@ec2-13-37-170-167.eu-west-3.compute.amazonaws.com << EOF
cd moneyes_api/
git stash
git pull
git stash pop
exit
EOF