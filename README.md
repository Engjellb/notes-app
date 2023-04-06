# notess-app
An app to maintain notes. Its files are organized in Model-View-Controller design pattern.  
Routes imitate **MVC's** routes that are regulated in **.htaccess** file.  

In term of app's content it contains simple, advanced and full-text search.  
Users can like, favorite and comment to notes and see which ones have chosen and liked.  

Some of request and response are done with axios. There are also composer dependencies like twig template engine, sfiwtmailer etc.    

**Programming language**: PHP  
**Databases**: MySQL, SQLite

## Getting started

install composer dependencies through CLI:

    composer install

Copy `.env.example` to `.env` file and fill with your required environment values

    cp .env.example .env
