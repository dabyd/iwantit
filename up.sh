zip -r proyecto.zip . -x 'public/uploads/*' -x 'public/uploads/*/*' -x 'public/uploads/*/*/*' -x 'storage/logs/*' -x '/.git/*'
scp ./proyecto.zip ubuntu@demo.i-want-it.es:~
rm proyecto.zip
ssh ubuntu@demo.i-want-it.es
