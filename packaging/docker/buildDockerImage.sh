#!/bin/env bash
#export DEBUG=true


# Define BUILD_FOLDER
pushd $(dirname $0)
DOCKER_FILENAME=$PWD/Dockerfile
BUILD_FOLDER=$PWD
popd

DOCKER_REPO=konvergence/ltb-ssp
export IMAGE_BUILD="1"

# Release build is the last SVN release that include all previous build release for client or server
RELEASE_MAJOR="1"
RELEASE_MINOR="3.0-beta"
LTB_RELEASE=${RELEASE_MAJOR}.${RELEASE_MINOR}




# Release Build of the Docker Image
# Final Docker Image Name
IMAGE_TAG=${LTB_RELEASE}-r${IMAGE_BUILD}




#-------------------------------------------------------------------------------------------------------
echo "Building image  ${DOCKER_REPO}:${IMAGE_TAG} ..."
#BUILD_OPTIONS="--force-rm=true --no-cache=true"
docker build $BUILD_OPTIONS -t ${DOCKER_REPO}:${IMAGE_TAG}  \
        --build-arg RELEASE_MAJOR=${RELEASE_MAJOR} \
        --build-arg RELEASE_MINOR=${RELEASE_MINOR} \
        --build-arg LTB_RELEASE=${LTB_RELEASE} \
        --file ${DOCKER_FILENAME} \
        ${BUILD_FOLDER}

        if [ $? -eq 0 ]; then
	       docker tag ${DOCKER_REPO}:${IMAGE_TAG} ${DOCKER_REPO}:${LTB_RELEASE}
           docker tag ${DOCKER_REPO}:${IMAGE_TAG} ${DOCKER_REPO}:latest
		else
		    echo docker builld return error code $?
        fi
	echo "Local Image created: ${DOCKER_REPO} "
	docker images ${DOCKER_REPO}
