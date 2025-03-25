# FIT3048 Project: SiteX

Created by Team 10:
- Daniel Ward
- Ali Maskari
- Yolanda Mao
- Clinton Bao

Further developed by Team 122:
- Alexander Martin
- Olivia Wu
- Harry Wang
- Rachelle Tran
- Tommy Hu

Then developed further still by Team 42:
- Ezra Isma
- Keh Luen (Leo) Chang
- Samantha (Sam) Tan
- Lan Nguyen
- Xiaofeng (Zu) Zhu

Default Database Credentials:
- Username: siteX
- Password: siteX
- Database: siteX

[Click here for database schema and sample data](https://git.infotech.monash.edu/UGIE/ugie-2022/team10/team10-app_fit3048/-/blob/master/sitex_schema.sql)

Sample Credentials:

Builder
- Username: builder@mail.com
- Password: 12345678

- Username: dwar@student.monash.edu
- Password: password

Contractor
- Username: contractor@mail.com
- Password: 12345678

- Username: joe@mail.com
- Password: password

On-site Worker
- Username: worker@mail.com
- Password: 12345678

- Username: joe@mail.com
- Password: password

On-site Worker
- Username: admin@mail.com
- Password: 12345678

# Recommended Installation Procedure

After this application is obtained from the git repository, please follow these steps before performing any other works:

1. Run `composer install` (to install PHP libraries that Camelot depends on)
2. Perform a [Database Reset](#database-initialization-and-reset) (to create relevant tables in your database)
3. (Optional) Toggle [Demonstration mode](#demonstration-mode) (so that you can log in as an administrator)
4. Run [Built-in Server](#built-in-sever) if it is under development

## Starting Application Using the Built-in Sever

> **Note:** For development purpose only.
> For production, you would use a production web server such as Apache2 or Nginx.

To load the built-in web server, type this command from your terminal from the same directory as this git repository:

```
bin/cake server
```

Follow the prompt to access the site, typically by opening a browser and navigating to `http://localhost:8765/`.

## Update Mailgun Details To Test/Deploy Email Functionality
In config/app.php:
Update Mailgun 'from', 'domain' and 'apiKey'.
Use test domain and apikey for testing
For deployment, update these attributes with production values.
