.PHONY: run-web, stop-web

PWD := $(shell pwd)
USER := $(shell id -u)
GROUP := $(shell id -g)

run-web: 
	cd docker && sudo docker-compose -p "time-$(USER)" up

stop-web: 
	cd docker && sudo docker-compose -p "time-$(USER)" stop 
