# LittleHome First Installation Guide

At first you need to place the index.php, config.php.sample and the src directory into the directory of your website (for example `/var/www/html' if it will be the main site of your apache installation). In order to facilitate version updates, this can be done by performing a checkout of the git branch corresponding to version you want to install from the littlehome repository. 

> git clone -b 1.16 https://github.com/cristianolongoodhl/littlehome.git opendatahacklab.org

Ensure that the web site directory, aside the files just placed there, has the web server user (usually `www-data`) as owner.

> chown -R www-data opendatahacklab.org
> chgrp -R www-data opendatahacklab.org

Now copy the `config.php.sample` to a new file `config.php` and here update 
constants as they fit your environment. Probaly you can leave them unchanged without any trouble.

Now you can enter your site at path src/admin.php and start the little home configuration.
