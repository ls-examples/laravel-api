PROJECT_NAME := books-server
DOCKER_REGISTRY := registry.prod3.dsxack.com

.PHONY: build
build:
	docker build -t ${DOCKER_REGISTRY}/${PROJECT_NAME} .

.PHONY: push
push:
	docker push ${DOCKER_REGISTRY}/${PROJECT_NAME}


