#!/bin/bash

cd ../

# Junta todos os argumentos passados como uma única string
COMMIT_MSG="$*"

# # Comandos git
git add .
git commit -m "$COMMIT_MSG"
git push -u origin main
