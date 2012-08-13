symfony2-propel-todomvc
=======================

Symfony2 + Propel variation around todomvc theme (see [http://addyosmani.github.com/todomvc/](http://addyosmani.github.com/todomvc/))

Big thanks to [@willdurand](https://github.com/willdurand) for his precious help

# How to install

configure your db connections in app/config/paramaters.yml

configure your [less](https://github.com/phiamo/MopaBootstrapBundle/blob/master/Resources/doc/less-installation.md)and yui compressor paths in app/config/config.yml. Something like:


``` yml
assetic:
    filters:
        less:
            node: /usr/local/bin/node
            node_paths: [/usr/local/lib/node_modules]
            apply_to: "\.less$"
        yui_css:
            jar: "%kernel.root_dir%/Resources/java/yuicompressor-2.4.7.jar"
        yui_js:
            jar: "%kernel.root_dir%/Resources/java/yuicompressor-2.4.7.jar"
```

Run in your shell the nexts commands lines

* download composer

``` bash
$ wget http://getcomposer.org/composer.phar
```

* install required bundles

``` bash
$ php composer.phar install
```

* chmod cache and log

``` bash
$ chmod -R 777 app/cache
$ chmod -R 777 app/logs
```

* publish assets

``` bash
$ php app/console assets:install web
```

* create the database 

``` bash
$ app/console propel:database:create
```

* build your model and your database

``` bash
$ app/console propel:build --insert-sql
```

* visit /app_dev.php/todos/

* generate assets

``` bash
$ app/console assetic:dump --env=prod --no-debug
```

* visit /app.php/todos/

# Roadmap (probably a new project)

* better structure for the backbone part
* think to gracefull degradation no javascript mod
* add TwitterBoostrap
* a myTodoMVC version with [FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle) and [FOSFacebookBundle](https://github.com/FriendsOfSymfony/FOSFacebookBundle.git)
