#!/usr/bin/env bash
# ================================================================
#  SAUVEGARDE MYSQL - SIRH Khadija Consulting
#  Linux / VPS - cree un dump SQL dans le dossier sauvegardes
#  Utilisation : sudo bash sauvegarde-mysql.sh
#  Variables optionnelles : DB_USER, DB_PASS, DB_NAME, BACKUP_DIR
# ================================================================
set -euo pipefail

DB_USER="${DB_USER:-root}"
DB_PASS="${DB_PASS:-}"
DB_NAME="${DB_NAME:-sirh_khadija}"
BACKUP_DIR="${BACKUP_DIR:-$(dirname "$0")/sauvegardes}"

mkdir -p "$BACKUP_DIR"
STAMP="$(date +%Y%m%d_%H%M%S)"
FILE="$BACKUP_DIR/${DB_NAME}_${STAMP}.sql"

if [ -n "$DB_PASS" ]; then
  mysqldump -u "$DB_USER" -p"$DB_PASS" --routines --triggers --single-transaction "$DB_NAME" > "$FILE"
else
  mysqldump -u "$DB_USER" --routines --triggers --single-transaction "$DB_NAME" > "$FILE"
fi

echo "Sauvegarde OK : $FILE"
# Rotation automatique : supprime les dumps de plus de 30 jours
find "$BACKUP_DIR" -name "*.sql" -mtime +30 -delete 2>/dev/null || true