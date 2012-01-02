# Support-Ticketing ChangeLog #

** v1.2 (2012-01-02) **

- New feature: "Quote time" added on ticket replies
- New feature: "Completion time" added on ticket replies
- Added a button to hide/show a ticket (useful to hide old tickets without deleting them)
- Added a link to show hidden tickets on the tickets list
- New feature: project graphic with some riepilogue data such as closed tickets + replies
- Copyright year updated on the files
- Migrating from the previous version: replace all the application files and re-config your includes/Mint/Config.php file
- Database migration: added "hidden" field on tickets table, added "quotetime" and "completedtime" fields on replies table. Please check the /Install-Database.sql file for further informations.

** v1.1 **

- Manage projects: you can create and delete projects from the interface
- New touch icon
- Many resources has been moved from http://static.squallstar.it/ to the internal resources
- Application version added on the footer
- jQuery has been updated to the last version
- How to migrate from the previous version: just replace all the application files and re-config your Config.php file


** v1.0 **

- First public release on GitHub