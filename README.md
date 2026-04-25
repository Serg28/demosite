# Termincin



## Getting started

To make it easy for you to get started with GitLab, here's a list of recommended next steps.

## Clone repository

- [ ] [Create](https://docs.gitlab.com/ee/user/project/repository/web_editor.html#create-a-file) or [upload](https://docs.gitlab.com/ee/user/project/repository/web_editor.html#upload-a-file) files
- [ ] [Add files using the command line](https://docs.gitlab.com/ee/gitlab-basics/add-file.html#add-a-file-using-the-command-line) or push an existing Git repository with the following command:

```
cd folder
git clone -b demo https://git.linecore.com/linecore-laravel/termincin.git .
cp .env.example .env
```

## Installation

- [ ] Run commands
```
composer install
composer run-script post-root-package-install
composer run-script post-create-project-cmd
```
- [ ] Edit .env file

- [ ] Run commands

```
php artisan migrate
php artisan optimize:clear

npm install
npx mix --production
```

## Generate admin password

- [ ] Run commands
```
php artisan admin:generatePassword
```
- [ ] AdminPanel: http://site.com/login

## Create Elasticsearch Index
- [ ] Run commands
```
php artisan elastic:create-index "App\ProductsIndexConfigurator"
php artisan scout:import "App\Models\Product"
```

## Integrate with your tools

- [ ] [Set up project integrations](https://gitlab.com/linecore-laravel/termincin/-/settings/integrations)