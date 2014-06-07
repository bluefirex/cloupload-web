# CloudApp Client for the browser

![Screenshot](http://f.cl.ly/items/2P3m2E1A1L333e0r2Z06/Image%202014-06-07%20at%2010.49.02%20nachm..png)

## Introduction

Cloupload Web is a simple CloudApp client for the browser. It is written in PHP and uses an SQLite-database (MySQL optional).

This client was tested in all webkit browsers.

## Features

- view up to 64 items
- audio and video previews
- search (!)
- view up to 64 trashed items
- grid and list view
- sort by type

## Installation

You need a web server running PHP 5.3 or newer. This can be a local installation of nginx/Apache/... or remote.

1. Upload the contents to your web server directory.
2. Open the URL of the script. It should redirect you to `setup.php`.
3. Enter your CloudApp account credentials.
4. Et voilá!

## Notes

This is a single user client. You should therefore put a password on the folder of the script (i.e. Basic Auth).

## Switching to MySQL

Cloupload Web spportes MySQL in case you don't want to use SQLite. Simple follow these steps to switch to MySQL:

1. Open `base.php`
2. Change the line saying `$db = Database::SQLite(PATH . '_db.sqlite3');` to `$db = Database::MySQL('host', 'username', 'password', 'database');`. Replace values as needed.
3. You're done!

## Increasing the file limit

Cloupload Web supports 64 files at once by default. If you need more than that, follow these steps to increase the limit:

1. Open `ajax/sync.php`
2. Change `64` to any value you like in both `$cloudApp->getItems` commands. If the value is too high, the CloudApp API will deny your request so you may need to try a few values.


## Logging out

Currently, there is no logout button in the UI. To log out of Cloupload, open `base.php` and write `$db->reset();` before the first `$db->setConfig` command.


## Planned features

- Uploading files
- Settings for logging out via UI
