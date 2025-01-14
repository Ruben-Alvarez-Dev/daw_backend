#!/bin/bash

# Mata cualquier proceso que esté usando el puerto 8000
lsof -ti:8000 | xargs kill -9 2>/dev/null

# Espera un segundo
sleep 1

# Inicia el servidor Laravel en el puerto 8000
php artisan serve --port=8000
