.PHONY: run-web, stop-web rm-web

PWD := $(shell pwd)
USER := $(shell id -u)
GROUP := $(shell id -g)

run-web: 
	cd docker && sudo docker-compose -p "time-$(USER)" up

stop-web: 
	cd docker && sudo docker-compose -p "time-$(USER)" stop 

rm-web: 
	cd docker && sudo docker-compose -p "time-$(USER)" rm 
