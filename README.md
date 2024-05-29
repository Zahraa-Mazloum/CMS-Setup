# Startup Website Project

## Install

### Gitlab SSH Connection

in your cmd.exe run command bellow:

``` shell
ssh-keygen -t rsa -b 2048 -C "your codendot email"
```

Then open `C:\Users\{USER}\.ssh\id_rsa.pub` in notepad++

- Select All : `ctrl + A`
- Copy Selected : `ctrl + V`

Open `https://gitlab.com/preferences` of your profile, click ssh keys and add your copied key with no expiration date.

### Startup Project Install

Open your terminal in new project folder

Execute the following command:

```shell
git clone git@gitlab.com:codendot/yii2-solutions/yii2-startup.git .
```

#### Prepaire Project

- Copy all files inside install folder into root and public.
- Configure .env file and connect to new database
- in terminal execute: `composer update -W --ignore-platform-reqs`
- the execute: `pnpm i`
- Install the migration by : `php yii migrate`
- Add new Root Admin User: `php yii make/user`
  - Name: `Root Admin`
  - Username: `rootAdmin`
  - password: `[[ a password from your choose ]]`
  - type: `developer` then yes
- open your admin: `[[ url ]]/cnd-admin`
- change admin path prefix in `_tiers.php` file located in config, ex: cnd-admin -> xyz-admin 

## Documentation

[Read Documentation](./docs/index.md)
