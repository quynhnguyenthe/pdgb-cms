#!/bin/bash

ECR_REGISTRY=134133618221.dkr.ecr.ap-northeast-1.amazonaws.com
ECR_REPOSITORY=plij-app-production
DOT_ENV_PARAM=plij-dot-env-production
CONTAINER_NAME=app
IMAGE_TAG=`echo $RANDOM | md5sum | head -c 20; echo;`

docker build -t $ECR_REPOSITORY:$IMAGE_TAG -f docker/Dockerfile --build-arg DOT_ENV="$(cat .env)" .
docker tag $ECR_REPOSITORY:$IMAGE_TAG $ECR_REGISTRY/$ECR_REPOSITORY:$IMAGE_TAG
echo "::set-output name=image::$ECR_REGISTRY/$ECR_REPOSITORY:$IMAGE_TAG"

docker stop plij
docker rm plij
docker run -it -p 3000:80 --name plij $ECR_REPOSITORY:$IMAGE_TAG
