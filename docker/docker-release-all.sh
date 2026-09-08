#!/bin/sh
set -e

IMAGE="fatoun2026/reservation-salles"
REPO_ROOT=$(pwd)
WORKTREE_DIR="/tmp/reservation-salles-release"

REUSSIS=""
IGNORES=""

rm -rf "$WORKTREE_DIR"

for TAG in $(git tag --sort=version:refname); do
    echo ""
    echo "=== ${TAG} ==="

    git worktree add --detach "$WORKTREE_DIR" "$TAG" > /dev/null 2>&1

    cp "$REPO_ROOT/Dockerfile" "$WORKTREE_DIR/Dockerfile" 2>/dev/null || true
    rm -rf "$WORKTREE_DIR/docker"
    cp -r "$REPO_ROOT/docker" "$WORKTREE_DIR/docker" 2>/dev/null || true

    if [ ! -f "$WORKTREE_DIR/composer.json" ]; then
        echo "Ignore : composer.json absent a ce stade du projet."
        IGNORES="${IGNORES} ${TAG}"
        git worktree remove --force "$WORKTREE_DIR"
        continue
    fi

    if ! (cd "$WORKTREE_DIR" && docker build -q -t "${IMAGE}:${TAG}" .); then
        echo "Echec du build pour ${TAG}, on continue avec le suivant."
        IGNORES="${IGNORES} ${TAG}"
        git worktree remove --force "$WORKTREE_DIR"
        continue
    fi

    docker push "${IMAGE}:${TAG}"
    REUSSIS="${REUSSIS} ${TAG}"
    git worktree remove --force "$WORKTREE_DIR"
done

DERNIER_TAG=$(echo "$REUSSIS" | tr ' ' '\n' | tail -1)
if [ -n "$DERNIER_TAG" ]; then
    docker tag "${IMAGE}:${DERNIER_TAG}" "${IMAGE}:latest"
    docker push "${IMAGE}:latest"
fi

echo ""
echo "=== Resume ==="
echo "Images poussees :${REUSSIS}"
echo "Tags ignores    :${IGNORES}"
