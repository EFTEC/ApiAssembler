<?php http_response_code(404); die(1); // it is a template, it is protected to be called directly ?>
<IfModule mod_rewrite.c>
    Options +FollowSymLinks
    RewriteEngine On

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ router.php?req=$1 [L,QSA]
    RewriteRule ^$ router.php?req=$1 [L,QSA]
    RewriteRule ^.*\.(bat|sh)$ - [F,L,NC]

</IfModule>
