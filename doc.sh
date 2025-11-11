php artisan config:clear
php artisan view:clear
php artisan cache:clear
rm -rf public/docs/*
mkdir public/docs/custom
cp ./custom-css-docs/* public/docs/custom/
php artisan scribe:generate

