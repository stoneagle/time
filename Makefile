.PHONY: run-web 
PWD := $(shell pwd)
USER := $(shell id -u)
GROUP := $(shell id -g)

run-web: 
	cd docker && sudo docker-compose up
