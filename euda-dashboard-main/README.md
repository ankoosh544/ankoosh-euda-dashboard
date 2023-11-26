# EUDA DASHBOARD

Nel file zip "AWS keys" troverai una cartella "AWS EC2" con all'interno
le chiavi per accedere ad EC2 tramite SSH e il file .env da utilizzare
sul server, io ho utilizzato putty quindi oltre alla chiave .pem c'è
anche la chiave .ppk che utilizza putty.

Sempre in "AWS keys" ci sono le credenziali sia per il servizio RDS che
per S3, in particolare le chiavi S3 sono le chiavi IAM generate da
Giuseppe sull'account root.

La dashboard è fatta completamente in Laravel + Filament, consiglio di
guardare la documentazione di Filament al seguente indirizzo:
<https://filamentphp.com/docs/2.x/admin/installation>

Per il caricamento della dashboard su EC2 ho seguito questi step:

-   Creazione istanza EC2 \*
-   Login via ssh con putty
-   Avviata la sessione la console chiederà "login as:" inserire
    "ubuntu"
-   Settaggio server Apache in EC2 \*
-   Generazione chiavi ssh legate all'account <git@github.com>
-   Caricamento codice su una repository github
-   Clonazione della repository tramite ssh nella directory del server
    apache "/var/www/github/"
-   Spostarsi nella cartella "/var/www/github/euda-dashboard" (comando:
    "cd /var/www/github/euda-dashboard")
-   Avvio di
- - > composer install
- - > npm install
- - > npm run build
-   Avvio di
> php artisan migrate:fresh  
- **Attezione**, questo comando va
    utilizzato solo se c'è necessità di resettare il database,
    altrimenti utilizzare il comando opportuno, lista comandi:
    <https://laravel.com/docs/10.x/migrations#running-migrations>
- Se sono settati alcuni dati nel seed
> php artisan db:seed
-   Creazione file .env tramite comando
> nano .env
-   e inserire
    all'interno il contenuto del file "AWS Keys/AWS EC2 SSH access/.env"
-   Riavviare il servizio apache tramite comando
>  sudo systemctl restart apache2

\*="Azione da non ripetere dopo la prima configurazione"

Link utili per eventuali problemi che si possono presentare durante il
deploy dell'app:

-   <https://laracasts.com/discuss/channels/laravel/permission-denied-on-storageframeworkviews>
-   <https://askubuntu.com/questions/857609/apache2-now-pointing-to-new-default-page>
-   <https://unix.stackexchange.com/questions/38978/where-are-apache-file-access-logs-stored>

Pagina con istanze EC2:
<https://eu-central-1.console.aws.amazon.com/ec2/home?region=eu-central-1#Instances>:

**Nel momento in cui viene riavviata un'istanza l'indirizzo IP
cambierà**

Se non avevate nessun seed configurato è possibile accedere al servizio
RDS tramite qualsiasi client mysql con i dati presenti all'interno di
"AWS Keys/RDS_Keys.txt",

- Per criptare la password potete utilizzare una qualsiasi linea di
comando con il comando "php artisan tinker"

- Poi "echo Hash::make(\'PASSWORD\');" sostituendo PASSWORD con la
password scelta

- Per altre informazioni potete guardare qui:
<https://stackoverflow.com/questions/22846897/how-to-create-a-laravel-hashed-password>


## Usefull links

- https://unix.stackexchange.com/questions/38978/where-are-apache-file-access-logs-stored
- https://www.ahtcloud.com/deploy-any-laravel-app-in-aws-amazon-web-services
- https://askubuntu.com/questions/857609/apache2-now-pointing-to-new-default-page
- https://laracasts.com/discuss/channels/laravel/permission-denied-on-storageframeworkviews
