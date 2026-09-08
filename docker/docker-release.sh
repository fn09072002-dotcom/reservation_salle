#!/bin/sh
# Construit et pousse l'image Docker sur Docker Hub, taguee avec le
# meme nom que le tag Git courant (ex: v1.0.0).
#
# Usage : se placer sur le commit tagge souhaite, puis lancer :
#   ./docker/docker-release.sh

set -e

IMAGE="fatoun2026/reservation-salles"

TAG=$(git describe --tags --exact-match 2>/dev/null) || {
    echo "Erreur : le commit courant (HEAD) n'est pas tagge."
    echo "Fais d'abord : git tag <version> puis relance ce script."
    exit 1
}

echo "Image     : ${IMAGE}"
echo "Tag       : ${TAG}"
echo ""

docker build -t "${IMAGE}:${TAG}" -t "${IMAGE}:latest" .

docker push "${IMAGE}:${TAG}"
docker push "${IMAGE}:latest"

echo ""
echo "Termine : ${IMAGE}:${TAG} et ${IMAGE}:latest sont disponibles sur Docker Hub."
