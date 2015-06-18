# forweb
Forweb framework
Flexible, scalable PHP open-source framework. 

Contain Core, DB, ORM, user, access, localization  modules.

To launch application copy files into your local host folder,
Open index.php file, find row

DB::init("localhost", "root", "", "php_db");

and set your database setting - host, username, userpassword, databasename.

Save changes, and then launch your apllication with query init=1
as an example: 
www.localhost.loc/?init=1

Framework will create ORM table classes, create tables in database, and add data.

After it open www.localhost.loc/ at the right side you will see simple login form.
default user email: admin@admin.admin
default user password: admin

after authorization you will see admin panel at left side of you site. Click 'configuration' link,
find there 'core' module, than find there 'url' field, populate it with you url - www.localhost.loc/ and save.
After it, you can do all, what you want.

Don't forget set modules/core/Core::DEVELOPMENT into false on working hosting.
