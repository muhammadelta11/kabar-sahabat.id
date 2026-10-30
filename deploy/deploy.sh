#!/usr/bin/env bash
#
# Deploy script Kabar Sahabat — untuk VPS dengan CloudPanel.
#
# ============================================================
# SETUP AWAL DI CLOUDPANEL (sekali saja):
# ============================================================
#
# 1. Buat site:
#    CloudPanel → + Add Site → Create a PHP Site
#    - Application : Generic
#    - Domain Name : kabarsahabat.id
#    - PHP Version : 8.4
#    Setelah jadi: buka site → ubah Root Directory, tambahkan /public di akhir.
#    (Root Directory jadi /home/kabar-sahabat/htdocs/kabarsahabat.id/public)
#
# 2. Buat database:
#    Site → Databases → tambah database & user (catat kredensialnya).
#
# 3. Pasang SSL:
#    Site → SSL/TLS → Let's Encrypt (pastikan DNS sudah mengarah ke server).
#
# 4. Node.js 22 (untuk build Vite) + Supervisor (untuk queue worker),
#    via SSH sebagai root:
#    curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
#    apt install -y nodejs supervisor
#
# ============================================================
# SETUP AWAL APLIKASI (SSH sebagai site user, sekali saja):
# ============================================================
#
# 5. Deploy key GitHub agar repo bisa di-pull:
#    ssh-keygen -t ed25519 -N "" -f ~/.ssh/id_ed25519
#    cat ~/.ssh/id_ed25519.pub   # -> GitHub repo → Settings → Deploy keys
#
# 6. Clone ke dalam folder htdocs/<domain> (hapus isian bawaan dulu):
#    cd ~/htdocs/kabarsahabat.id
#    rm -rf ./* ./.[!.]*
#    git clone git@github.com:sakhandaru/Kabarsahabat.git .
#
# 7. Environment:
#    cp deploy/.env.production.example .env
#    nano .env            # isi DB_DATABASE, DB_USERNAME, DB_PASSWORD
#    php artisan key:generate
#
# 8. Deploy pertama:
#    chmod +x deploy/deploy.sh && ./deploy/deploy.sh
#
# 9. Queue worker:
#    sudo cp deploy/supervisor-kabarsahabat.conf /etc/supervisor/conf.d/
#    sudo supervisorctl reread && sudo supervisorctl update
#
# Catatan: ganti "kabar-sahabat" di file ini dan di supervisor conf
#          jika nama site user kamu berbeda.
# ============================================================

set -euo pipefail

APP_DIR="$HOME/htdocs/kabarsahabat.id"
BRANCH="main"

cd "$APP_DIR"

echo "==> Pull kode terbaru dari origin/$BRANCH"
git pull origin "$BRANCH"

echo "==> Install dependency PHP (tanpa dev)"
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

echo "==> Build frontend"
npm ci --ignore-scripts
npm run build

echo "==> Migrasi database"
php artisan migrate --force

echo "=> Storage link"
[ -e public/storage ] || php artisan storage:link

echo "==> Cache config, route, view, event"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

if command -v supervisorctl >/dev/null 2>&1; then
    echo "==> Restart queue worker"
    sudo supervisorctl restart kabarsahabat-worker:* || true
fi

echo "✔ Deploy selesai: $(date)"
