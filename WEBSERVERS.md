# Web Servers
  
It is typical to use the front-controller pattern to funnel appropriate HTTP requests received by your web server to a single PHP file. The instructions below explain how to tell your web server to send HTTP requests to your PHP front-controller file.

## PHP built-in server
Run the following command in terminal to start localhost web server, assuming ./public/ is public-accessible directory with index.php file:
```
php -S localhost:8888 -t public public/index.php
```

If you are not using index.php as your entry point then change appropriately.

## Apache configuration
Ensure your .htaccess and index.php files are in the same public-accessible directory. The .htaccess file should contain this code:
```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```
This .htaccess file requires URL rewriting. Make sure to enable Apache’s mod_rewrite module and your virtual host is configured with the AllowOverride option so that the .htaccess rewrite rules can be used:
```
AllowOverride All
```

## Nginx configuration
This is an example Nginx virtual host configuration for the domain example.com. It listens for inbound HTTP connections on port 80. It assumes a PHP-FPM server is running on port 9000. You should update the server_name, error_log, access_log, and root directives with your own values. The root directive is the path to your application’s public document root directory; your Slim app’s index.php front-controller file should be in this directory.
```
server {
    listen 80;
    server_name example.com;
    index index.php;
    error_log /path/to/example.error.log;
    access_log /path/to/example.access.log;
    root /path/to/public;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ \.php {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param SCRIPT_NAME $fastcgi_script_name;
        fastcgi_index index.php;
        fastcgi_pass 127.0.0.1:9000;
    }
}
```
