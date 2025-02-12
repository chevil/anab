A.N.a.B. is a collaborative audio annotation tool
that can be used for different purposes :
transcription, comments on music, story telling, ...

Notes can include images and links to make reference
to some other ressources and enrich the audio archives.

You can aggregate your notes in an audio book
and generate it for use on all your devices.

It is based on the wavesurfer.js library
that uses the Web Audio Api
to process audio within the browser.

===== PRELIMINARY =====

This project should be installed in an HTTPS zone
and the installation should be made under the account
running apache, usually www-data:www-data.

===== PREREQUISITES =====

* A LAMP server
  php requires php-mbstring and php-xml

* ffmpeg, ffprobe, mimetype ( from libfile-mimeinfo-perl on ubuntu )

* zip

* optionally, if you want to use AI,
you need OpenAI whisper,
install it with :
pip3 install openai-whisper

( you have the ability to enable/disable the use of AI for each user )

===== INSTALL =====

* clone the repository in your server's accesible contents

> git clone https://github.com/chevil/anab.git

> cd anab

> cd sql

* edit wavesurfer.sql and set your admin password : <your-password>

* create the database :

> mysqladmin create wavesurfer

* fill the wavesurfer database with :

> mysql wavesurfer < wavesurfer.sql

> cd ..

* edit config.php and change these lines according
to your mysql configuration :

$config['dbname'] = "wavesurfer";

$config['dbhost'] = "__host__";

$config['dbuser'] = "__dbuser__";

$config['dbpass'] = "__dbpass__";

$config['owner'] = "admin";

> cd ..

All the files in the end must belong to www-data:www-data :

chown -R www-data:www-data anab

optionally, if you want to use AI,
install OpenAI Whisper with :

pip3 install openai-whisper

You're set, log in to the system as admin
and create users, archives and books.

concept and programming : chevil@giss.tv

initial concept & design : californiadream@free.fr

testing and advices : jaume@nualart.cat
