symfony2-propel-todomvc
=======================

Symfony2 + Propel variation around todomvc theme (see [http://addyosmani.github.com/todomvc/](http://addyosmani.github.com/todomvc/))

Big thanks to [@willdurand](https://github.com/willdurand) for his precious help

# How to install

configure db connections, and your social parameters in app/config/paramaters.yml

configure [less](https://github.com/phiamo/MopaBootstrapBundle/blob/master/Resources/doc/less-installation.md) and [yui compressor](http://yuilibrary.com/download/yuicompressor/) paths in app/config/config.yml. Something like:


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

* load fixtures

``` bash
$ app/console propel:fixtures:load @MazenoviTodoMVCBundle
```

* visit /app_dev.php/todos/

* generate assets

``` bash
$ app/console assetic:dump --env=prod --no-debug
```

* visit /app.php/todos/

# Roadmap

* fix toggle backbone feature
* update tests
* introduce date in todo
* geolocate todo
* visual search on user, title, date and location
* think to gracefull degradation no javascript mod
* <s>better structure for the backbone part</s>
* <s>add TwitterBoostrap</s>
* <s>a myTodoMVC version with [FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle)</s>
