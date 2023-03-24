#!/bin/bash

pg_dump -U moneyes -d moneyes -t asset > /tmp/db/asset.sql