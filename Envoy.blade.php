@servers(['dev' => 'tcars@18.197.199.183 -p ooYol7ai beeJ3loh'])

@task('deploy_dev', ['on' => ['dev']])
cd /var/www/tcars/dev.tcarservice.com
git pull origin develop -q
npm run prod
/usr/bin/php8.1 artisan migrate --force
@endtask

@task('update_dev', ['on' => ['dev']])
cd /var/www/tcars/dev.tcarservice.com
git pull origin develop -q
npm run prod
@endtask