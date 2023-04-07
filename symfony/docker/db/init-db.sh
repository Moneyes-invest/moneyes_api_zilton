#!/bin/bash

psql -U moneyes -d moneyes < /tmp/db/init.sql
