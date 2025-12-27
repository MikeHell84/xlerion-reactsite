#!/usr/bin/env bash
set -e
rsync -av --delete ../backup/ ./
git add .
git commit -m "Backup sync $(date -u +%Y-%m-%dT%H:%M:%SZ)" || true
git push
