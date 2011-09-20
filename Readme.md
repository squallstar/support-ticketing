# Support-Ticketing system

Support-Ticketing is a micro-application that lets you to manage your project issues and gives your customers the opportunity to create tickets and discuss about them.
Under the hood, the ticketing system is made with **PHP5** and powered by **Mint**, a micro-framework by Squallstar Studio.
Currently it supports only **MySQL** databases!

# Features

 * Manage infinite projects and customers
 * Every project can be associated to any number of customers and can have infinite tickets
 * Every user can open tickets on its associated projects, and can also reply to the tickets (such as a discussion).
 * Any Tickets or replies can have an attached file
 * Tickets have different statuses: inserted, assigned, discussing, closed.
 * E-mail notifications for the owner/account of the project and also for each assigned account.
 * Desktop and Mobile interface (iPhone and Android like)
 * It's extremely fast, and it's open-source :)

# How to install

1. Upload the application folder in the root of your virtualhost

2. Config the database settings and the website url/email here: includes/Mint/Config.php

3. Execute the SQL contained in the Install-database.sql file

4. You're done!

# Screenshot

![Screenshot](http://static.squallstar.it/images/support-screen.png)

# Online demo

You can try an online demo with a customer account here:

 * http://support.squallstar.it
 * Username: demo
 * Password: demo

# Notes

 * Currently the only available language is Italian
 * The only supported Database is **MySQL**
 * You need to create new projects and users via the database (we are working to implement a creation UI)

# Resources

 * [Project homepage](https://github.com/squallstar/support-ticketing)

# Contribute via GitHub

To contribute through GitHub, first of all fork the main Support-ticketing repository.
Then, checkout your new fork and type this line into the terminal to stay updated with the main repo:

 * git remote add upstream git://github.com/squallstar/support-ticketing.git

Now you can pull the upstream updates anytime you want via these commands:

 * git fetch upstream
 * git merge upstream/master
