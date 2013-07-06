/**
 * Generic template place holder class.
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @version 1.1.0
 * @author Jason Davis (jason.davis.fl@gmail.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */

Delete the \cnf\ folder and the \etc\apps\ . So your app and panel configs can stay the same

Some Features added
- Custom Theme Module Icon Overrides
- Sidebar Menu
- Sidebar Menu Collapse/Expand with Cookie Memory
- Module Category boxes Collapse/Expand with Cookie Memory
- Module Category boxes Drag and Drop Positioning with Database memory per user
- Breadcrumbs on Module pages
- Show/hide stats
- Upgrade to Bootstrap 3 beta
- Upgrade to jQuery from v1.4 to v2.0

//// GIT WORK FLOW ///////

GIT PULL

Fork a repo

Pull it to my computer

do like 20 commits

Send it upstream to my Forked repo

Send my 20 commites over to the Repo that I forked it from oriniganly as a Pull Request

////////////////////////////////



3/20/2013 Git updated

Created - dryden\ui\tpl\modulelistzsidebar.class.php
Returns a Sidebar menu HTML

Re-wrote - dryden\ui\tpl\modulelistznavbar.class.php
Returns the new Nav menu HTML

Created - dryden\ui\tpl\breadcrumbs.class.php
Shows Breadcrumb links

EDITED dryden\ui\module.class.php
lINE 249:Added a function GetModuleCategoryName()  to get the current loaded modules Name and  Category name

EDITED dryden\ui\moduleloader.class.php
LINE 25: Edited the GetModuleCats() function to return sorted order from drag drop positions

EDITED dryden\ctrl\users.class.php
LINE 67: Added $userdetail->addItemValue('catorder', $dbvals['ac_catorder_vc']);
GetUserDetail()

Create  - \dryden\ajax\moduleorder.php
AJAX request to here saves Module category positions on the homepage

EDITED \dryden\ui\tpl\progbardisk.class.php
Added bootstrap progress bar

EDITED \dryden\ui\tpl\progbarbandwidth.class.php
Added bootstrap progress bar

EDITED \dryden\ui\tpl\clientdomains.class.php
Added bootstrap progress bar


4/9/2013
EDITED modules\dns_manager\code\controller.ext.php

4/19/2013
EDITED \dryden\ui\sysmessage.class.php
Redid Notice class to use Bootstrap notices

EDITED \dryden\ui\tpl\notice.class.php
Made this template call \dryden\ui\sysmessage.class.php

4/20/2013
ADDED \dryden\ui\tpl\modulelistjson.class.php
Retuns a list of Modules from the Database as a JSON object for the new JavaScript Typeahead functionality
















505 Pull Request
-        $fulladdress = $address . "@" . $domain;
-        $fulladdress = str_replace(' ', '', $fulladdress);
-        $fulladdress = strtolower($fulladdress);
+        $fulladdress = strtolower(str_replace(' ', '', $address . '@' . $domain));
/////////////////////////////////////////


dryden/debug/phperrors.class.php
dryden/loader.inc.php
dryden/runtime/hook.class.php
dryden/ctrl/users.class.php
dryden/ui/template.class.php
dryden/ui/templateparser.class.php

dryden/ui/tpl/subdomains.class.php
dryden/ui/tpl/totaldistlists.class.php
dryden/ui/tpl/totaldomains.class.php
dryden/ui/tpl/totalemail.class.php
dryden/ui/tpl/totalftp.class.php
dryden/ui/tpl/totalmysql.class.php
dryden/ui/tpl/totalparkeddomains.class.php
dryden/ui/tpl/totalsubdomains.class.php
dryden/ui/tpl/usagebandwidth.class.php
dryden/ui/tpl/usagediskspace.class.php
dryden/ui/tpl/useddistlists.class.php
dryden/ui/tpl/useddomains.class.php
dryden/ui/tpl/usedemail.class.php
dryden/ui/tpl/usedforwarders.class.php
dryden/ui/tpl/usedftp.class.php
dryden/ui/tpl/usedmysql.class.php
dryden/ui/tpl/usedparkeddomains.class.php
dryden/ui/tpl/usedsubdomains.class.php

modules/distlists/code/controller.ext.php
modules/domains/code/controller.ext.php
modules/domains/module.zpm
modules/forwarders/code/controller.ext.php
modules/ftp_management/code/controller.ext.php
modules/mailboxes/code/controller.ext.php
modules/mailboxes/module.zpm
modules/manage_clients/module.zpm
modules/mysql_databases/code/controller.ext.php
modules/packages/module.zpm
modules/parked_domains/code/controller.ext.php
modules/sub_domains/code/controller.ext.php
modules/sub_domains/module.zpm
modules/updates/hooks/OnDaemonDay.hook.php
modules/usage_viewer/code/controller.ext.php

etc/styles/zpanelx/css/default.css
etc/styles/zpanelx/login.ztml
etc/styles/zpanelx/master.ztml

inc/init.inc.php // might not be valid for E_Notice



// Runs query every single time this is called
dryden\ctrl\users.class.php
GetUserDetail($uid = "")






dryden/debug


1) Debug
I am using the amazing ChromePHP http://www.chromephp.com/ to debug the AJAX request and the way ZPanel includes so many files, it's veryuseful for debuging as it logs any message you want to the Google Chrome Dev Tools Console

It is the Chrome version of FirePHP which is the same thing but for Firefox.  So if you do not mind I would like to include the chromePHP class in the existing /panel/dryden/debug/ folder if thats ok with you?  It will just sit there so it can be used when needed?

Below is a simple demo of usage....

ChromePhp::log('hello world');
ChromePhp::log($_SERVER);

key/value
ChromePhp::log($key, $value);

// warnings and errors
ChromePhp::warn('this is a warning');
ChromePhp::error('this is an error');


2)



/////////////////     OPTIMIZE QUERIES ////////////////////////////////

/dryden/ui/moduleloader.class.php

FILE: /dryden/ctrl/users.class.php
$user = ctrl_users::GetUserDetail();
GetUserDetail Ran:  348
This Function called something like 39 times in the Dryden codebase alone

This doesn't need to be done RIGHT now but I really think some things like this should be fixed.  For example that query returns an array of user details from the user MYSQL table.  So this could be ran 1 time and stored in a variable for use by the other code

Idealy when taht function is ran, it will check is the variable holding the result exist or not, if it exist will return the existing variable and if not it will query and populate the variable.









/////////////////     OPTIMIZE QUERIES ////////////////////////////////














Mobile
http://codedevelopr.com/screenshots/2013-03-17_12-16-52.png

iPad view
http://codedevelopr.com/screenshots/2013-03-17_12-17-57.png

Drag and Drop Full screen
http://codedevelopr.com/screenshots/2013-03-17_12-19-22.png


Home page full screen
http://codedevelopr.com/screenshots/2013-03-17_11-40-33.png

Home page full screen with Custom Module Icons
http://codedevelopr.com/screenshots/2013-03-17_11-53-12.png

Home page full screen with Menu's visible
http://codedevelopr.com/screenshots/2013-03-17_11-43-56.png

Home page mobile view
http://codedevelopr.com/screenshots/responsive-menu.png

Another Mobile/Tablet View
http://codedevelopr.com/screenshots/2013-03-17_11-43-06.png

Module Page w/Breadcrumbs
http://codedevelopr.com/screenshots/2013-03-17_11-47-54.png
