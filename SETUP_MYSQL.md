# MySQL Setup untuk Linux WSL2

Aplikasi ini memerlukan MySQL. Jalankan perintah berikut:

```bash
# Install MySQL Server
sudo apt update
sudo apt install -y mysql-server

# Start MySQL service
sudo service mysql start

# Buat database (password default kosong untuk user root)
sudo mysql -u root << 'EOF'
CREATE DATABASE IF NOT EXISTS festival_inovasi;
CREATE DATABASE IF NOT EXISTS festival_inovasi_test;
FLUSH PRIVILEGES;
EOF
```

Setelah MySQL terinstall dan database dibuat, lanjutkan:

```bash
# Run migrations
php artisan migrate

# Run seeders (akan membuat superadmin + data master)
php artisan db:seed

# Verify dengan test
php artisan test
```

Kredensial superadmin default (dari `.env`):
- Email: `admin@festival-inovasi.local`
- Password: `admin123`

**Penting**: Ganti password superadmin setelah login pertama kali di production.
