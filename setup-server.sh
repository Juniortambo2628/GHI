#!/bin/bash
# GHI Server Initial Setup Script
# Run this ONCE on the server via SSH before the first deploy.
# Usage: ssh -p 1624 jhoffkau@54.37.142.31 'bash -s' < setup-server.sh

set -euo pipefail

DEPLOY_BASE="/home/jhoffkau"
SHARED_DIR="${DEPLOY_BASE}/GHI-shared"
CORE_LINK="${DEPLOY_BASE}/GHI-core"

echo "=== GHI Server Setup ==="
echo ""

# 1. Create shared directory structure
echo "==> Creating shared directories..."
mkdir -p "${SHARED_DIR}/storage/app/public"
mkdir -p "${SHARED_DIR}/storage/logs"
mkdir -p "${DEPLOY_BASE}/GHI-releases"

# 2. Copy existing .env if present in current app root
if [ -f "${DEPLOY_BASE}/GHI-core/.env" ]; then
    echo "==> Copying existing .env to GHI-shared..."
    cp "${DEPLOY_BASE}/GHI-core/.env" "${SHARED_DIR}/.env"
fi

# 3. Copy existing uploads if present
if [ -d "${DEPLOY_BASE}/GHI-core/storage/app/public" ] && [ "$(ls -A "${DEPLOY_BASE}/GHI-core/storage/app/public" 2>/dev/null)" ]; then
    echo "==> Copying existing uploads to GHI-shared..."
    cp -r "${DEPLOY_BASE}/GHI-core/storage/app/public/"* "${SHARED_DIR}/storage/app/public/" 2>/dev/null || true
fi

# 4. Create the public_html/api symlink (if not already set up)
if [ ! -L "${DEPLOY_BASE}/public_html/api" ]; then
    echo "==> Creating public_html/api symlink..."
    mkdir -p "${DEPLOY_BASE}/public_html"
    # Remove existing directory if empty, or warn if not
    if [ -d "${DEPLOY_BASE}/public_html/api" ]; then
        if [ "$(ls -A "${DEPLOY_BASE}/public_html/api" 2>/dev/null)" ]; then
            echo "WARNING: public_html/api exists and is not empty."
            echo "  Move or remove it before creating the symlink."
            echo "  Example: mv public_html/api public_html/api.bak"
        else
            rmdir "${DEPLOY_BASE}/public_html/api"
            ln -sfn "${CORE_LINK}/public" "${DEPLOY_BASE}/public_html/api"
            echo "  Symlink created."
        fi
    else
        ln -sfn "${CORE_LINK}/public" "${DEPLOY_BASE}/public_html/api"
        echo "  Symlink created."
    fi
else
    echo "==> public_html/api symlink already exists."
fi

# 5. Ensure .env exists in shared directory
if [ ! -f "${SHARED_DIR}/.env" ]; then
    echo ""
    echo "WARNING: No .env file found."
    echo "  Create one at: ${SHARED_DIR}/.env"
    echo "  You can copy from .env.example and fill in your values."
fi

echo ""
echo "=== Setup Complete ==="
echo ""
echo "Directory structure:"
echo "  ${SHARED_DIR}/"
echo "    .env                          <- your config (never overwritten)"
echo "    storage/app/public/           <- uploads (persistent)"
echo "    storage/logs/                 <- logs (persistent)"
echo "  ${DEPLOY_BASE}/GHI-releases/   <- versioned releases"
echo "  ${CORE_LINK}                    <- symlink to current release"
echo ""
echo "Next steps:"
echo "  1. Add the public key to ~/.ssh/authorized_keys:"
echo "     cat github-actions-GHI.pub >> ~/.ssh/authorized_keys"
echo "  2. Ensure your .env is in place:"
echo "     nano ${SHARED_DIR}/.env"
echo "  3. Push to main to trigger the first deploy."
