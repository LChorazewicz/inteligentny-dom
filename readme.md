na start:



#baza
 ```bin/console doctrine:schema:update --force```
 
 ```bin/console doctrine:migrations:migrate```
 
 
 
#instalacja plików konfiguracyjnych supervisor-a
 ```sudo bin/console app-install-application dev /etc/supervisor/conf.d/```
- dev - srodowisko (możliwe dev i prod)
- /etc/supervisor/conf.d/ - ścieżka do katalogu z plikami konfiguracyjnymi supervisor-a
- sudo service supervisor restart