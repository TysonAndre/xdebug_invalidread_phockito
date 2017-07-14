#!/bin/bash
docker build -t xdebug-test-phockito:2.5.5 .
docker run xdebug-test-phockito:2.5.5
