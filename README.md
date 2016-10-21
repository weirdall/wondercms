# WonderCMS
WonderCMS - re-built from scratch

## Improvements
- Used Object-oriented programming style.
- Separate logic from markup for readability and maintainability.
- Used JSON as database, is lightweight and easy to read and write, also to avoid creation of many files and separate config from pages.
- Fixed all security holes (Local File Inclusion, Cross-site scripting).
- Used `password_hash()` instead of `md5()`. MD5 can be brute-forced too easily.
- Password can be changed after login successfully, which make sense to me.
- Used [Trumbowyg](https://alex-d.github.io/Trumbowyg) as default editor.
- Other improvements...

## 20161018
## Fork from: https://github.com/yassineaddi/wondercms


## Nginx config
    server {
        listen 80;
        listen [::]:80;
        
        server_name example.com;
        root /var/www/example;
    
    
        # Add index.php to the list if you are using PHP
        index index.html index.php;
    
    
        location / {
            # First attempt to serve request as file, then index.php
    	    try_files $uri @rewrite;
        }
    
        # Rewrite to index.php
        location @rewrite {
            rewrite ^/(.*)$ /index.php?object=$1;
        }
    
        # Deny access 
        location ~ (config.js|db.js) {
            deny all;
        }
    
        # needed for letsencrypt
        location ~ /.well-known {
            allow all;
        }
    
        # Enable PHP7 
        location ~ \.php$ {
            include snippets/fastcgi-php.conf;
        }
    }


