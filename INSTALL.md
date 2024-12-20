A.N.a.B. is a collaborative audio annotation tool
that can be used for different purposes :
transcription, translation, comments on music, story telling, ...

Notes can include images and links to make reference
to some other ressources and enrich the audio archives.

You can aggregate your notes in an audio book
and generate it for use on all your devices.

It is based on the wavesurfer.js library
that uses the Web Audio Api
to process audio within the browser.

===== PREREQUISITES =====

* A LAMP server
  with a php with 
  php-mbstring and php-xml

* ffmpeg, ffprobe, mimetype from libfile-mimeinfo-perl

* zip

===== INSTALL =====

* clone the repository

> git clone https://github.com/chevil/anab.git
> cd anab

* create the database :
> cd sql

> mysqladmin create wavesurfer

* change the admin password in wavesurfer.sql

> mysql wavesurfer < wavesurfer.sql

* edit config.php and change these lines according
to your mysql configuration :

$config['dbname'] = "wavesurfer";

$config['dbhost'] = "__host__";

$config['dbuser'] = "__dbuser__";

$config['dbpass'] = "__dbpass__";

$config['owner'] = "admin";

You're set, log in to the system as admin
and create users, archives and books.

concept and programming : chevil@giss.tv
initial concept & design : beatrice.rettig@gmail.com
testing and advices : jaume@nualart.cat
