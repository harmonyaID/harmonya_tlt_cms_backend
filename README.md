# Installation & Configuration

### Installation boilerplate

### Environment
```text
# Nanti rubah dengan domain operational jika sudah up ke operational
APP_URL=http://dev.api.newin-app.mobi

VERSION=v2
SERVICE_NAME=newin

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=root

FIREBASE_SERVER_KEY=<firebase-server-key>

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=c22c3d77659d47
MAIL_PASSWORD=bf006cb86ab1ff
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@newin-app.com
MAIL_FROM_NAME="${APP_NAME}"

POSTMARK_TOKEN=<postmark-token>

FORGOT_PASSWORD_LINK_STAFF=http://localhost:8000/reset-password
FORGOT_PASSWORD_LINK_PARTNER=http://localhost:8000/reset-password
FORGOT_PASSWORD_LINK_MEMBER=

```

### Configuration
```shell
php artisan migrate & php artisan migrate --path=database/migrations/tmp
OR
php artisan migrate:fresh & php artisan db:seed

php artisan optimize
php artisan optimize:clear
```

### Login
Staff: admin@example.com / adminnewin101 \
Partner: partner@example.com / partnernewin101
