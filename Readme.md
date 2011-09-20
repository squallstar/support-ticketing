# SUPPORT-TICKETING

Support-Ticketing is a micro-application that let you manage your project issues and let your customers to create tickets and discuss about them.
Under the hood, the framework is **Mint** by Squallstar Studio.

# How to install

1. Upload the application folder in the root of your virtualhost

2. Config the database settings and the website url/email here: includes/Mint/Config.php

3. Execute the SQL contained in the Install-database.sql file

4. You're done!

# Online demo

You can try an online demo with a customer account here:

 * http://support.squallstar.it
 * //Username: demo
 * //Password: demo

# Resources

 * [Project homepage](https://github.com/squallstar/support-ticketing)

# Contribute via GitHub

To contribute through GitHub, first of all fork the main Support-ticketing repository.
Then, checkout your new fork and type this line into the terminal to stay updated with the main repo:

 * git remote add upstream git://github.com/squallstar/support-ticketing.git

Now you can pull the upstream updates anytime you want via these commands:

 * git fetch upstream
 * git merge upstream/master
