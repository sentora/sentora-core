.. _setup:

Installation
============

phpMyAdmin does not apply any special security methods to the MySQL
database server. It is still the system administrator's job to grant
permissions on the MySQL databases properly. phpMyAdmin's :guilabel:`Users`
page can be used for this.

.. warning::

    :term:`Mac` users should note that if you are on a version before
    :term:`Mac OS X`, StuffIt unstuffs with :term:`Mac` formats. So you'll have
    to resave as in BBEdit to Unix style ALL phpMyAdmin scripts before
    uploading them to your server, as PHP seems not to like :term:`Mac`-style
    end of lines character ("``\r``").

.. _quick_install:

Quick Install
+++++++++++++

#. Choose an appropriate distribution kit from the phpmyadmin.net
   Downloads page. Some kits contain only the English messages, others
   contain all languages. We'll assume you chose a kit whose name 
   looks like ``phpMyAdmin-x.x.x -all-languages.tar.gz``.
#. Untar or unzip the distribution (be sure to unzip the subdirectories):
   ``tar -xzvf phpMyAdmin_x.x.x-all-languages.tar.gz`` in your
   webserver's document root. If you don't have direct access to your
   document root, put the files in a directory on your local machine,
   and, after step 4, transfer the directory on your web server using,
   for example, ftp.
#. Ensure that all the scripts have the appropriate owner (if PHP is
   running in safe mode, having some scripts with an owner different from
   the owner of other scripts will be a problem). See :ref:`faq4_2` and
   :ref:`faq1_26` for suggestions.
#. Now you must configure your installation. There are two methods that
   can be used. Traditionally, users have hand-edited a copy of
   :file:`config.inc.php`, but now a wizard-style setup script is provided
   for those who prefer a graphical installation. Creating a
   :file:`config.inc.php` is still a quick way to get started and needed for
   some advanced features.


Manually creating the file
--------------------------

To manually create the file, simply use your text editor to create the
file :file:`config.inc.php` (you can copy :file:`config.sample.inc.php` to get
minimal configuration file) in the main (top-level) phpMyAdmin
directory (the one that contains :file:`index.php`). phpMyAdmin first
loads :file:`libraries/config.default.php` and then overrides those values
with anything found in :file:`config.inc.php`. If the default value is
okay for a particular setting, there is no need to include it in
:file:`config.inc.php`. You'll need a few directives to get going, a
simple configuration may look like this:

.. code-block:: php

    
    <?php
    $cfg['blowfish_secret'] = 'ba17c1ec07d65003';  // use here a value of your choice
    
    $i=0;
    $i++;
    $cfg['Servers'][$i]['auth_type']     = 'cookie';
    ?>

Or, if you prefer to not be prompted every time you log in:

.. code-block:: php

    
    <?php
    
    $i=0;
    $i++;
    $cfg['Servers'][$i]['user']          = 'root';
    $cfg['Servers'][$i]['password']      = 'cbb74bc'; // use here your password
    $cfg['Servers'][$i]['auth_type']     = 'config';
    ?>

For a full explanation of possible configuration values, see the 
:ref:`config` of this document.

.. index:: Setup script

.. _setup_script:

Using Setup script
------------------

Instead of manually editing :file:`config.inc.php`, you can use the `Setup
Script <setup/>`_. First you must manually create a folder ``config``
in the phpMyAdmin directory. This is a security measure. On a
Linux/Unix system you can use the following commands:

.. code-block:: sh

    
    cd phpMyAdmin
    mkdir config                        # create directory for saving
    chmod o+rw config                   # give it world writable permissions

And to edit an existing configuration, copy it over first:

.. code-block:: sh

    
    cp config.inc.php config/           # copy current configuration for editing
    chmod o+w config/config.inc.php     # give it world writable permissions

On other platforms, simply create the folder and ensure that your web
server has read and write access to it. :ref:`faq1_26` can help with
this.

Next, open ``setup/`` in your browser. If you have an existing configuration,
use the ``Load`` button to bring its content inside the setup panel.
Note that **changes are not saved to disk until explicitly choose ``Save``**
from the *Configuration* area of the screen. Normally the script saves the new
:file:`config.inc.php` to the ``config/`` directory, but if the webserver does
not have the proper permissions you may see the error "Cannot load or
save configuration." Ensure that the ``config/`` directory exists and
has the proper permissions - or use the ``Download`` link to save the
config file locally and upload it (via FTP or some similar means) to the
proper location.

Once the file has been saved, it must be moved out of the ``config/``
directory and the permissions must be reset, again as a security
measure:

.. code-block:: sh

    
    mv config/config.inc.php .         # move file to current directory
    chmod o-rw config.inc.php          # remove world read and write permissions
    rm -rf config                      # remove not needed directory

Now the file is ready to be used. You can choose to review or edit the
file with your favorite editor, if you prefer to set some advanced
options which the setup script does not provide.

#. If you are using the ``auth_type`` "config", it is suggested that you
   protect the phpMyAdmin installation directory because using config
   does not require a user to enter a password to access the phpMyAdmin
   installation. Use of an alternate authentication method is
   recommended, for example with HTTP–AUTH in a :term:`.htaccess` file or switch to using
   ``auth_type`` cookie or http. See the :ref:`faqmultiuser`
   for additional information, especially :ref:`faq4_4`.
#. Open the `main phpMyAdmin directory <index.php>`_ in your browser.
   phpMyAdmin should now display a welcome screen and your databases, or
   a login dialog if using :term:`HTTP` or
   cookie authentication mode.
#. You should deny access to the ``./libraries`` and ``./setup/lib``
   subfolders in your webserver configuration. For Apache you can use
   supplied :term:`.htaccess`  file in that folder, for other webservers, you should
   configure this yourself. Such configuration prevents from possible
   path exposure and cross side scripting vulnerabilities that might
   happen to be found in that code.
#. It is generally good idea to protect public phpMyAdmin installation
   against access by robots as they usually can not do anything good
   there. You can do this using ``robots.txt`` file in root of your
   webserver or limit access by web server configuration, see
   :ref:`faq1_42`.

.. index:: 
    single: Configuration storage
    single: phpMyAdmin configuration storage
    single: pmadb

.. _linked-tables:

phpMyAdmin configuration storage
++++++++++++++++++++++++++++++++

For a whole set of new features (bookmarks, comments, :term:`SQL`-history,
tracking mechanism, :term:`PDF`-generation, column contents transformation,
etc.) you need to create a set of special tables.  Those tables can be located
in your own database, or in a central database for a multi-user installation
(this database would then be accessed by the controluser, so no other user
should have rights to it).

Please look at your ``./examples/`` directory, where you should find a
file called *create\_tables.sql*. (If you are using a Windows server,
pay special attention to :ref:`faq1_23`).

If you already had this infrastructure and upgraded to MySQL 4.1.2 or
newer, please use :file:`examples/upgrade_tables_mysql_4_1_2+.sql`
and then create new tables by importing
:file:`examples/create_tables.sql`.

You can use your phpMyAdmin to create the tables for you. Please be
aware that you may need special (administrator) privileges to create
the database and tables, and that the script may need some tuning,
depending on the database name.

After having imported the :file:`examples/create_tables.sql` file, you
should specify the table names in your :file:`config.inc.php` file. The
directives used for that can be found in the :ref:`config`. You will also need to
have a controluser with the proper rights to those tables (see section
:ref:`authentication_modes` below).

.. _upgrading:

Upgrading from an older version
+++++++++++++++++++++++++++++++

Simply copy :file:`config.inc.php` from your previous installation into
the newly unpacked one. Configuration files from old versions may
require some tweaking as some options have been changed or removed.
For compatibility with PHP 6, remove a
``set_magic_quotes_runtime(0);`` statement that you might find near
the end of your configuration file.

You should **not** copy :file:`libraries/config.default.php` over
:file:`config.inc.php` because the default configuration file is version-
specific.

If you have upgraded your MySQL server from a version previous to 4.1.2 to
version 5.x or newer and if you use the phpMyAdmin configuration storage, you
should run the :term:`SQL` script found in
:file:`examples/upgrade_tables_mysql_4_1_2+.sql`.

.. index:: Authentication mode

.. _authentication_modes:

Using authentication modes
++++++++++++++++++++++++++

:term:`HTTP` and cookie authentication modes are recommended in a **multi-user
environment** where you want to give users access to their own database and
don't want them to play around with others. Nevertheless be aware that MS
Internet Explorer seems to be really buggy about cookies, at least till version
6. Even in a **single-user environment**, you might prefer to use :term:`HTTP`
or cookie mode so that your user/password pair are not in clear in the
configuration file.

:term:`HTTP` and cookie authentication
modes are more secure: the MySQL login information does not need to be
set in the phpMyAdmin configuration file (except possibly for the 
:config:option:`$cfg['Servers'][$i]['controluser']`).
However, keep in mind that the password travels in plain text, unless
you are using the HTTPS protocol. In cookie mode, the password is
stored, encrypted with the blowfish algorithm, in a temporary cookie.

.. note: 
   
    This section is only applicable if your MySQL server is running
    with ``--skip-show-database``. 

For ':term:`HTTP`' and 'cookie' modes, phpMyAdmin needs a controluser that has
**only** the ``SELECT`` privilege on the *`mysql`.`user` (all columns except
`Password`)*, *`mysql`.`db` (all columns)*, *`mysql`.`host` (all columns)* and
*`mysql`.`tables\_priv` (all columns except `Grantor` and `Timestamp`)* tables.
You must specify the details for the controluser in the :file:`config.inc.php`
file under the :config:option:`$cfg['Servers'][$i]['controluser']` and
:config:option:`$cfg['Servers'][$i]['controlpass']` settings. The following
example assumes you want to use ``pma`` as the controluser and ``pmapass`` as
the controlpass, but **this is only an example: use something else in your
file!** Input these statements from the phpMyAdmin :term:`SQL` Query window or
mysql command–line client. Of course you have to replace ``localhost`` with the
webserver's host if it's not the same as the MySQL server's one. 

If you want to use the many new relation and bookmark features:  (this of
course requires that your :ref:`linked-tables` be set up).

.. code-block:: mysql
   
   GRANT USAGE ON mysql.* TO 'pma'@'localhost' IDENTIFIED BY 'pmapass';
   GRANT SELECT (
   Host, User, Select_priv, Insert_priv, Update_priv, Delete_priv,
   Create_priv, Drop_priv, Reload_priv, Shutdown_priv, Process_priv,
   File_priv, Grant_priv, References_priv, Index_priv, Alter_priv,
   Show_db_priv, Super_priv, Create_tmp_table_priv, Lock_tables_priv,
   Execute_priv, Repl_slave_priv, Repl_client_priv
   ) ON mysql.user TO 'pma'@'localhost';
   GRANT SELECT ON mysql.db TO 'pma'@'localhost';
   GRANT SELECT ON mysql.host TO 'pma'@'localhost';
   GRANT SELECT (Host, Db, User, Table_name, Table_priv, Column_priv)
   ON mysql.tables_priv TO 'pma'@'localhost';
   
If you want to use the many new relation and bookmark features:
   
.. code-block:: mysql
   
   GRANT SELECT, INSERT, UPDATE, DELETE ON <pma_db>.* TO 'pma'@'localhost';
   
(this of course requires that your phpMyAdmin
configuration storage be set up).
   
Then each of the *true* users should be granted a set of privileges
on a set of particular databases. Normally you shouldn't give global
privileges to an ordinary user, unless you understand the impact of those
privileges (for example, you are creating a superuser).
For example, to grant the user *real_user* with all privileges on
the database *user_base*:
   
.. code-block:: mysql
   
   GRANT ALL PRIVILEGES ON user_base.* TO 'real_user'@localhost IDENTIFIED BY 'real_password';
   
   
What the user may now do is controlled entirely by the MySQL user management
system. With HTTP or cookie authentication mode, you don't need to fill the
user/password fields inside the :config:option:`$cfg['Servers']`.

.. index:: pair: HTTP; Authentication mode

HTTP authentication mode
------------------------

* Uses :term:`HTTP` Basic authentication
  method and allows you to log in as any valid MySQL user.
* Is supported with most PHP configurations. For :term:`IIS` (:term:`ISAPI`) 
  support using :term:`CGI` PHP see :ref:`faq1_32`, for using with Apache 
  :term:`CGI` see :ref:`faq1_35`.
* See also :ref:`faq4_4` about not using the :term:`.htaccess` mechanism along with
  ':term:`HTTP`' authentication mode.

.. index:: pair: Cookie; Authentication mode

.. _cookie:

Cookie authentication mode
--------------------------

* You can use this method as a replacement for the :term:`HTTP` authentication 
  (for example, if you're running :term:`IIS`).
* Obviously, the user must enable cookies in the browser, but this is
  now a requirement for all authentication modes.
* With this mode, the user can truly log out of phpMyAdmin and log in
  back with the same username.
* If you want to log in to arbitrary server see :config:option:`$cfg['AllowArbitraryServer']` directive.
* As mentioned in the :ref:`require` section, having the ``mcrypt`` extension will
  speed up access considerably, but is not required.

.. index:: pair: Signon; Authentication mode

Signon authentication mode
--------------------------

* This mode is a convenient way of using credentials from another
  application to authenticate to phpMyAdmin.
* The other application has to store login information into session
  data.

.. seealso::
    :config:option:`$cfg['Servers'][$i]['auth_type']`,
    :config:option:`$cfg['Servers'][$i]['SignonSession']`,
    :config:option:`$cfg['Servers'][$i]['SignonScript']`,
    :config:option:`$cfg['Servers'][$i]['SignonURL']`


.. index:: pair: Config; Authentication mode

Config authentication mode
--------------------------

* This mode is the less secure one because it requires you to fill the
  :config:option:`$cfg['Servers'][$i]['user']` and
  :config:option:`$cfg['Servers'][$i]['password']`
  fields (and as a result, anyone who can read your :file:`config.inc.php`
  can discover your username and password).  But you don't need to setup
  a "controluser" here: using the :config:option:`$cfg['Servers'][$i]['only_db']` might be enough.
* In the :ref:`faqmultiuser` section, there is an entry explaining how
  to protect your configuration file.
* For additional security in this mode, you may wish to consider the
  Host authentication :config:option:`$cfg['Servers'][$i]['AllowDeny']['order']`
  and :config:option:`$cfg['Servers'][$i]['AllowDeny']['rules']` configuration directives.
* Unlike cookie and http, does not require a user to log in when first
  loading the phpMyAdmin site. This is by design but could allow any
  user to access your installation. Use of some restriction method is
  suggested, perhaps a :term:`.htaccess` file with the HTTP-AUTH directive or disallowing
  incoming HTTP requests at one’s router or firewall will suffice (both
  of which are beyond the scope of this manual but easily searchable
  with Google).

.. index:: pair: Swekey; Authentication mode

.. _swekey:

Swekey authentication mode
--------------------------

The Swekey is a low cost authentication USB key that can be used in
web applications. When Swekey authentication is activated, phpMyAdmin
requires the users's Swekey to be plugged before entering the login
page (currently supported for cookie authentication mode only). Swekey
Authentication is disabled by default. To enable it, add the following
line to :file:`config.inc.php`:

.. code-block:: php
    
    $cfg['Servers'][$i]['auth_swekey_config'] = '/etc/swekey.conf';

You then have to create the ``swekey.conf`` file that will associate
each user with their Swekey Id. It is important to place this file
outside of your web server's document root (in the example, it is
located in ``/etc``). A self documented sample file is provided in the
``examples`` directory. Feel free to use it with your own users'
information. If you want to purchase a Swekey please visit
`http://phpmyadmin.net/auth\_key <http://phpmyadmin.net/auth_key>`_
since this link provides funding for phpMyAdmin.

.. seealso:: :config:option:`$cfg['Servers'][$i]['auth_swekey_config']`


Securing your phpMyAdmin installation
+++++++++++++++++++++++++++++++++++++

The phpMyAdmin team tries hardly to make the application secure, however there
are always ways to make your installation more secure:

* remove ``setup`` directory from phpMyAdmin, you will probably not 
  use it after initial setup
* prevent access to ``libraries`` directory from browser, 
  as it is not needed, supplied ``.htaccess`` file does this
* properly choose authentication method - :ref:`cookie`
  is probably the best choice for shared hosting
* in case you don't want all MySQL users to be able to access 
  phpMyAdmin, you can use :config:option:`$cfg['Servers'][$i]['AllowDeny']['rules']` to limit them
* consider hiding phpMyAdmin behind authentication proxy, so that 
  MySQL credentials are not all users need to login
