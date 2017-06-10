Patch 2.0 (Refer Item Details page for Patch logs)

* Notice for Super Store Finder Multi Categories Add-on: 

a. You need to purchase Super Store Finder at http://codecanyon.net/item/super-store-finder/3630922 prior to using this Add-on
b. Be sure to backup your existing files and database before performing the steps below

1. Alter cat_id column in stores table to varchar(255) in your database, run below SQL command:

ALTER TABLE stores CHANGE cat_id cat_id varchar(255);

2. If you're using the standard version (non-responsive) extract and replace content within superstorefinder.zip to your installation folder

3. If you're using responsive version, extract and replace content within superstorefinder_responsive.zip to your installation folder.

You're done!

Support and Inquiries: http://codecanyon.net/user/highwarden

