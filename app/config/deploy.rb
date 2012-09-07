set :application, "MyTodo"
set :user,        "ssh.mytodo.m4z3.me"
set :domain,      "mytodo.m4z3.me"
set :deploy_to,   "/var/www/#{domain}"
set :app_path,    "app"

set :repository,  "file:///opt/lampp/htdocs/symfony2-propel-todomvc"
set :deploy_via,  :copy
set :scm,         :git
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `subversion`, `mercurial`, `perforce`, or `none`

set :model_manager, "propel"
# Or: `propel`

role :web,        domain                         # Your HTTP server, Apache/etc
role :app,        domain                         # This may be the same as your `Web` server
role :db,         domain, :primary => true       # This is where Symfony2 migrations will run

set  :keep_releases,  3

set :shared_files,        ["app/config/parameters.yml"]
set :shared_children,     [app_path + "/logs", web_path + "/uploads", "vendor"]
set :use_composer, true
set :dump_assetic_assets, true

set :use_sudo, false

# php composer.phar update pour les symlink du twitter bootstrap
# php app/console assetic:dump génération du less
# php app/console propel:build --insert-sql
# php app/console propel:fixtures:load @MazenoviTodoMVCBundle
# cp -R var/www/clients/client0/web15/shared/vendor/friendsofsymfony/user-bundle/FOS/UserBundle/Propel/om vendor/friendsofsymfony/user-bundle/FOS/UserBundle/Propel/
# cp -R var/www/clients/client0/web15/shared/vendor/friendsofsymfony/user-bundle/FOS/UserBundle/Propel/map vendor/friendsofsymfony/user-bundle/FOS/UserBundle/Propel/

# Be more verbose by uncommenting the following line
# logger.level = Logger::MAX_LEVEL