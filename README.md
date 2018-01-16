The "squared" Moodle Theme
======================

The theme is generally based on squares. If you like squares, that is definately your first choice, if you do not like squares, 
you might consider it as well, because it looks really good ;-).

The idea of this theme is to put design in first place, and then see if it can be done technically. This is the first theme most of
the users do not recognize it was made for moodle ;-). The authors even pretend that it is the most beautiful theme ;-). 

About the theme.
 - It uses a lot of squares: blocks are squared, some heading elements are squared and so on.
 - New icon set for moodle standard activities and plugins. If you want the source (vector), just contact info@edulabs.org
 - It is not yet responsive. If you would like it to have responsive, we are square enough to accept money to do that. Just write to info@edulabs.org
 - The frontpage slideshow is completely customizable concerning text and images. Changing animation style: you have to be a coder.
 - Your social icons appear as squares on the bottom of the theme
 - Blocks are collapsed per default
 - The theme uses the font Source Sans Pro from Adobe, which is optimised for web reading
 - There is a super dropdown custom menu. You can add categories to the custom menu. The menu adds all content of the category
   to the custom menu according to the rights of the user.
   Example setting in the general them settings /admin/settings.php?section=themesettings :
   My category name|http://example.com/moodle/course/index.php?categoryid=2
   My other category name|http://example.com/moodle/course/index.php?categoryid=1
   Third category name|http://example.com/moodle/course/index.php?categoryid=3
 - You are not allowed to copy the frontpage and colors of www.virtuelle-ph.at 
 
Copyright notice
===============
The theme is copyrighted by "Onlinecampus Virtuelle PH" http://www.virtuelle-ph.at

Created by
David Bogner             | project lead and programming    | http://www.edulabs.org
John Stabinger           | programming                     | https://moodle.org/user/profile.php?id=691370
contemas websolutions OG | design                          | http://www.contemas.net

Maintained by
=============
David Bogner | http://www.edulabs.org

Free Software
=============
The squared theme is 'free' software under the terms of the GNU GPLv3 License.

It can be obtained for free from:
http://moodle.org/plugins/view.php?plugin=theme_squared

You have all the rights granted to you by the GPLv3 license.  If you are unsure about anything, then the
FAQ - http://www.gnu.org/licenses/gpl-faq.html - is a good place to look.

If you reuse any of the code then I kindly ask that you make reference to the theme.

If you make improvements or bug fixes then I would appreciate if you would send them back to me by forking from
https://github.com/dasistwas/moodle-theme_squared and doing a 'Pull Request' so that the rest of the
Moodle community benefits.

Donations
=========
This theme is provided to you for free, and if you want to express your gratitude for using this theme, please consider contracting us
in order to improve the theme or add your settings.

Required version of Moodle
==========================
This version works with Moodle version 2014051200.00 release 2.7 (Build: 20140512) and above within the 2.7 branch until the
next release.

Installation
============
 1. Ensure you have the version of Moodle as stated above in 'Required version of Moodle'.  This is important as the
    theme relies on underlying core code that is out of our control.
 2. Login as an administrator and put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
 3. Copy the extracted 'squared' folder to the '/theme/' folder.
 4. Go to 'Site administration' -> 'Notifications' and follow standard the 'plugin' update notification.
 5. Select as the theme for the site.
 6. Put Moodle out of Maintenance Mode.

Upgrading
=========
 1. Ensure you have the version of Moodle as stated above in 'Required version of Moodle'.  This is important as the
    theme relies on underlying core code that is out of our control.
 2. Login as an administrator and put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
 3. Make a backup of your old 'squared' folder in '/theme/' and then delete the folder.
 4. Copy the replacement extracted 'squared' folder to the '/theme/' folder.
 5. Go to 'Site administration' -> 'Notifications' and follow standard the 'plugin' update notification.
 6. If automatic 'Purge all caches' appears not to work by lack of display etc. then perform a manual 'Purge all caches'
    under 'Home -> Site administration -> Development -> Purge all caches'.
 7. Put Moodle out of Maintenance Mode.

Uninstallation
==============
 1. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
 2. Change the theme to another theme of your choice.
 3. In '/theme/' remove the folder 'squared'.
 4. Put Moodle out of Maintenance Mode.

Reporting issues
================
Before reporting an issue, please ensure that you are running the latest version for your release of Moodle.  It is important
that you are operating the required version of Moodle as stated at the top - this is because the theme relies on core
functionality that is out of its control.

When reporting an issue you can check he issue list https://github.com/dasistwas/moodle-theme_squared/issues and if the problem
 does not exist, create an issue.

It is important that you provide as much information as possible, the critical information being the contents of the theme's 
'version.php' file.  Other version information such as specific Moodle version, theme name and version also helps. A screen shot
can be really useful in visualising the issue along with any files you consider to be relevant.
