#!/bin/bash
tag=`echo $RANDOM | md5sum | head -c 20; echo;`
cd /opt/plij/
git pull
docker build -t plij:$tag -f docker/Dockerfile .
docker stop plij
docker rm plij
docker run -ti -d -p 80:80 --name plij plij:$tag
