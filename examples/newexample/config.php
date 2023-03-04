<?php http_response_code(404); die(1); // it is a configuration file. This line prevents to display it online ?>
{
    "databasetype": "mysql",
    "server": "127.0.0.1",
    "user": "root",
    "password": "abc.123",
    "database": "api-assembler",
    "questionencryption": "yes",
    "encryptionpassword": "abc.123",
    "encryptionsalt": "1222",
    "encryptioniv": "yes",
    "encryptionmethod": "aes-256-ctr",
    "hashmethod": "sha256",
    "namespace": "examples\\newexample",
    "composerpath": "..\/..\/vendor",
    "folderrepo": "repo",
    "folderapi": "api",
    "tablesSelected": [
        "productcategories",
        "products",
        "users"
    ],
    "classes": [
        "ProductCategory",
        "Product",
        "User"
    ],
    "classMethods": {
        "ProductCategory": [
            {
                "name": "listall",
                "type": "listall",
                "verb": "ALL",
                "argument1": ""
            }
        ],
        "Product": [
            {
                "name": "listall",
                "type": "listall",
                "verb": "ALL",
                "argument1": ""
            }
        ],
        "User": [
            {
                "name": "listall",
                "type": "listall",
                "verb": "ALL",
                "argument1": ""
            }
        ]
    },
    "questionroute": "yes",
    "questionaccess": "yes",
    "questiondev": "prod",
    "machineid": "seg",
    "baseurl_dev": "https:\/\/www.seg.cl\/api\/examples\/newexample",
    "baseurl_prod": "https:\/\/www.seg.cl\/api\/examples\/newexample",
    "templateurl": "",
    "questioncache": "yes",
    "cache_type": "redis",
    "cache_server": "127.0.0.1",
    "cache_schema": "",
    "cache_port": "6379",
    "cache_user": "",
    "cache_password": ""
}