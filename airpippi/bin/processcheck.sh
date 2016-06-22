#!/bin/bash

isAlive=`ps -ef | grep "twittercrawler.py" | grep -v grep | wc -l`
if [ $isAlive != 1 ]; then
	python /opt/airpippi/bin/twittercrawler.py &
fi
