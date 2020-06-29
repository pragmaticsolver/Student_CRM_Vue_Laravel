# BCE

## Setup Project In localhost

### Requirements
- PHP 7.2 or greater
- composer
- MySql 5.7
- Node

### Steps
- Clone Project From git
- copy .env.example to .env
- update .env to point correct db
- composer install
- php artisan key:generate
- npm install
- php artisan migrate
- php artisan db:seed
- php artisan ziggy:generate "resources/generated/ziggy.js"
- npm run dev OR npm run watch

- use php -S localhost:8888 - instead of php artisan serve (if facing error with php artisan serve)

### Commands to run when changing branches in localhost

npm install
composer install
php artisan ziggy:generate "resources/generated/ziggy.js"
php artisan permission:cache-reset
npm run dev / npm run watch

Npm command to do this all
`npm run branch-changed`

### Git workflow

#### Golden Rules
 - Always create new branch from `master` branch
 - Never ever create new branch from `test` branch

#### Step 1
- Create new branch for each new feature (from master)
- for e.g. we create 3 branches feauture-1, feature-2, feature-3

#### Step 2
- when development is done for particular feature, merge that branch with "test" branch and push it to test server.
- for e.g. when development for feature-1 is completed, merge that branch into test branch (and deploy to test server)
- similarly when development for feature-2 is completed, merge that branch into test branch (and deploy to test server)
- Likewise for all branches

#### Step 3
- Once the particular feature is tested and approved on test server, we can merge that feature branch into master
- for e.g. feature-1 and feture-2 are complete - merge those both branch into master (and deploy to live server)
- for e.g. fetaure-3 needs more updates - continue work on that branch, once done follow step 2 above

#### Execptions
- Development for feauture-1 is done and pushed to test server, but not merged into master yet, and you need to work on feature-2 that needs all the changes of feature-1 then you can create new branch from master branch called feature-2 and merge feature-1 branch into feature-2 branch you just created.

## Projct Instances

### bcekt

- URL: https://app.benchaneikaiwa.com/
- Path: /var/www/vhosts/benchaneikaiwa.com/app.benchaneikaiwa.com
- Old Url: http://uteach.jp/bcekt
- Old Path: /var/www/vhosts/uteach.jp/httpdocs/bcekt

### bcejr
- URL: https://app.benchanjr.com/
- Path: /var/www/vhosts/benchanjr.com/app.benchanjr.com
- Old Url: https://www.uteach.jp/bcejr
- Old Path: /var/www/vhosts/uteach.jp/httpdocs/bcejr

### test
- URL: https://www.uteach.jp/test/
- Path: /var/www/vhosts/uteach.jp/httpdocs/test (symlink)

### bce online
- URL: https://online.benchaneikaiwa.com/
- Path: /var/www/vhosts/benchaneikaiwa.com/bce_online

### setuptest
- URL: https://www.uteach.jp/setuptest/
- Path: /var/www/vhosts/uteach.jp/httpdocs/setuptest (symlink)

### pacificenglish
- URL: https://www.uteach.jp/pacificenglish/
- Path: /var/www/vhosts/uteach.jp/httpdocs/pacificenglish (symlink)

### waxtakumidou
- URL: https://www.uteach.jp/waxtakumidou/
- Path: /var/www/vhosts/uteach.jp/httpdocs/waxtakumidou (symlink)

### englishstudio
 - URL: https://www.uteach.jp/englishstudio
 - Path: /var/www/vhosts/uteach.jp/httpdocs/englishstudio_code (symlink)

## Queue Process Management

### View Status of que workers
1. SSH as root user
2. `supervisorctl status`

### Add Remove que workers
1. SSH as root user
2. nano /etc/supervisord.conf
3. supervisorctl reload
4. supervisorctl status

### Notes
- `supervisorctl` - to enter into interactive mode 
- `help` - to view all commands)

- https://stackoverflow.com/a/47322239/6336363
- https://stackoverflow.com/a/42911758/6336363

### supervisorctl sample config

[program:laravel-worker-bcekt]
process_name=%(program_name)s_%(process_num)02d
command=/opt/plesk/php/7.2/bin/php /var/www/vhosts/benchaneikaiwa.com/bcekt/current/artisan queue:work --tries=3 --queue=line,stripe,emails,default
autostart=true
autorestart=true
user=root
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/vhosts/benchaneikaiwa.com/bcekt/current/worker.log


## New Instance Creation.
- Add details in deploy.php file
- dep firstdeploy {projectname}
- [If not private subdoamin] create sybmolic link
- - ln -sf {deploy_path}/current {symbolic_file} e.g. ln -sf /var/www/vhosts/uteach.jp/httpdocs/mariko_code/current mariko
- create new db & user.
- update details in .env file
- dep deploy {projectname} - (It will also seed required data for first time)
- add instance details to "Instances" section above.
- Cron Job Setup (Create new scheduled task from plesk interface)
- Que Setup
- Remove cache - cd {code path}/storage/framework/cache/data && rm -fr *
- copy storage folder if needed.  e.g. rsync -avzh /var/www/vhosts/benchaneikaiwa.com/bcekt/current/storage/app/public/* /var/www/vhosts/benchaneikaiwa.com/bce_online/current/storage/app/public/
- Check Settings on server
    upload_max_filesize = 64MB
    post_max_size = 64MB
- update deploy-master script in package.json file

## Other Notes
- Sytem generated timestamps are stored in UTC, other dates & time are stored in as it is so it will be considered to be in timezone set in school settings page.
- to use specific version of php use `/opt/plesk/php/7.2/bin/php`

### Merge with lang branch

git checkout 1-language-files-modifications \
git pull \
git merge master \
git push \
git checkout master \
git merge 1-language-files-modifications --squash


### Deployer notes

dep ssh {host} - login into current version forlder for given project

### Ziggy Integration

php artisan ziggy:generate "resources/generated/ziggy.js" - need to run this command whenever route files are being modified.


## Ngrock
ngrok http laradock-bcenew_apache2_1:443
laradock-bcenew_apache2_1:443 - is a docker image running apache to view its status
run command docker-compose ps to view that name or status

## Import database in local
docker exec -i laradock-bcenew_mysql_1 mysql -uroot -proot bce < /home/vinay/code-details/bce/local_db.sql


## Deployment

#### Deploy to master sites
`npm run deploy-master`

#### Deploy to test site
`npm run deploy-test`

## Custom Seeder Implementation
#### Goal:
Run a seeder file only once (per instance), same as how laravel migration works.

#### Components:

##### Custom Seeder File Generator
`php artisan make:custom_seeder` (handled by custom console command named `CreateCustomSeederFile.php`)

##### Custom Seeder File Runner

`php artisan db:seed` (Same as how seeding works normally, handled by `DatabaseSeeder.php` file)
