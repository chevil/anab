#!/bin/bash

echo $1

strstr() {
  [ "${1#*$2*}" = "$1" ] && echo "0"
  echo "1"
}

if [ $# -ne 1 ]; then
   echo "ERR: Usage: $0 <file_url>"
   exit 1
fi

dirname=`grep -rl "$1" archives/*/index.php | cut -f2 -d'/'`
echo "$dirname" > /tmp/anab-archive-dir.txt

while read dir
do

echo "Directory : $dir"
cp -f archives/template/*.js "archives/$dir"
cp -f archives/template/*.php "archives/$dir"
sed -i "s#__file_url__#$1#g" "archives/$dir/app.js"
sed -i "s#__file_url__#$1#g" "archives/$dir/appl.js"
sed -i "s#__file_url__#$1#g" "archives/$dir/index.php"
chmod -R 777 "archives/$dir"

done < /tmp/anab-archive-dir.txt

rm /tmp/anab-archive-dir.txt
