# Start local
im using basic LAMP docker server:


## Prerequisites
- (WINDOWS) Install WSL
```PowerShell
wsl --update
```
- Install docker [docker.com/products/docker-desktop/](https://www.docker.com/products/docker-desktop/)

## Start
in `\infrastructura\docker` folder run:
```
docker compose build
docker compose up -d
```