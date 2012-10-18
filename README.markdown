symfony2-propel-todomvc
=======================

Symfony2 + Propel variation around todomvc theme (see [http://addyosmani.github.com/todomvc/](http://addyosmani.github.com/todomvc/))

Big thanks to [@willdurand](https://github.com/willdurand) and [@themouette](https://github.com/themouette) for their precious help

# How to install

* install [`nodejs`](http://nodejs.org/) and [`npm`](https://npmjs.org/) ([in 30 secondes](https://gist.github.com/579814#file_node_and_npm_in_30_seconds.sh))
* install [`grunt`](https://github.com/backbone-boilerplate/grunt-bbb#getting-started)
* install [`less`](https://github.com/phiamo/MopaBootstrapBundle/blob/master/Resources/doc/less-installation.md)

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

* create and custom your own parameters.yml

``` bash
$ cp app/config/parameters.yml.sample app/config/parameters.yml
```

* create the database 

``` bash
$ php app/console propel:database:create
```

* build your model and your database, load fixtures and acls with

``` bash
$ php app/console todomvc:build
```

* generate minified assets

``` bash
$ cd web/bundles/mazenovitodomvc/js
$ bbb debug
```

* visit /app_dev.php/todos/

you can login with todomvc:todomvc to test admin account, and with todomvcguest:todomvcguest to test simple user account

* visit /app.php/todos/

* demo http://mytodo.m4z3.me/todos/

# How to test

* install required bundles

``` bash
$ php composer.phar install --dev
```

* launch test suite

``` bash
$ php bin/behat @MazenoviTodoMVCBundle
```

# Roadmap

* introduce date in todo
* introduce geolocation todo with [BazingaGeocoderBundle](https://github.com/willdurand/BazingaGeocoderBundle) and [jquery-addresspicker](git://github.com/sgruhier/jquery-addresspicker.git)
* [visual search](http://documentcloud.github.com/visualsearch/) on user, title, date and location
* email notification with [Swiftmailer](https://github.com/symfony/SwiftmailerBundle)