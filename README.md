campick
=======

A simple LAMP app to take a list of images and let users chose amongst them. 
Intended purpose is ingest AXIS webcam feeds.

Note that this thing is hella vulnerability to pwnage. Should be resistant to 
SQLi and XSS, but no guarantees. Certainly no protections against clickfraud, 
etc.

Installation
============

Pretty simple. Spin up Apache, PHP5, MySQL. Create a database (Default 
'campick'), edit the top four lines of dbspecs.php to have appropriate db creds, 
then hit it. Set-up is automatic. You'll need to set an admin password, which 
really only lets you import a list of URLs, which should be images. Note that 
the admin password is salted and SHA256 hashed. :P

Credits
=======

Patrick Bogen (@pdbogen)

License
=======
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
