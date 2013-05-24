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
