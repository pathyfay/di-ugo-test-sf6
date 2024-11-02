#!/bin/bash

envsubst < /docker-entrypoint-initdb.d/init.sql > /docker-entrypoint-initdb.d/init.sql
