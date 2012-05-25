symfony2-propel-todomvc
=======================

Symfony2 + Propel variation around todomvc theme (see [http://addyosmani.github.com/todomvc/](http://addyosmani.github.com/todomvc/))

Big thanks to [@willdurand](https://github.com/willdurand) for his precious help

# How to install

configure your db connections in app/config/paramaters.yml

configure your compass, sass, and yuo compressor paths in app/config/config.yml. Something like:

``` yml
assetic:
    filters:
        compass:
            sass: /var/lib/gems/1.8/gems/sass-3.1.12/bin/sass
            bin: /var/lib/gems/1.8/gems/compass-0.11.7/bin/compass
        sass:
            bin: /var/lib/gems/1.8/gems/sass-3.1.12/bin/sass
            compass: /var/lib/gems/1.8/gems/compass-0.11.7/bin/compass
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
