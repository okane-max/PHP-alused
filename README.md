# Iseseisev töö nr3


## Paigaldusjuhend

1. Võta ette mingi Ubuntu Server LTS masin. Näiteks **22.04** LTS või **24.04** LTS.
2. Lae alla Apache ja Docker. `apt install apache2 -y` `apt install docker.io && apt install docker-compose-v2 -y`
3. Vabasta port **:80** apache teenuse käest. `systemctl stop apache2` ja `systemctl disable apache2`
4. Liigu veebiserveri juurkausta `cd /var/www/html`
5. Klooni projekt: `git clone https://github.com/okane-max/PHP-alused.git`
6. Liigu Github-i repositooriumi. `cd PHP-alused/`
7. Lase dockeril teha oma maagiat `docker compose up -d`.
8. Ava auto rendi veebisait brauseris aadressil `http://linux-serveri-aadress`, mille leiad kasutades `ip a`
9. Ava phpMyAdmin lehekülg aadressil `http://linux-serveri-aadress:8080`


## Tabelitesse andmete lisamine

1. Kui on soov lisada rohkem andmeid tabelitesse **users** ja/või **reservations** siis soovitan kasutada phpMyAdmin lehekülge.
2. Kui see ei meeldi siis on ka võimalik siseneda andmebaasi Linux masinal `docker exec -it autorent_db mysql -u root -pPar00l autorent`
3. Kasutajaid saab lisada järgmisel kujul:<br />
  ``INSERT INTO `users` (`username`, `password`, `email`) VALUES``<br>``('peeter_pakiauto', '$2y$10$9ZoNP0lDx0H6RgSZ.wrx..PpzDGPFNeRwnxUbAq/W7y9HlWq/lFEG', 'peeter@auto.ee');``
5. Reserveeringuid saab lisada järgmisel kujul:<br />
  ``INSERT INTO `reservations` (`user_id`, `car_id`, `start_date`, `end_date`, `total_price`, `status`) VALUES``<br>``(1, 4, '2026-06-03', '2026-06-13', 234.00, 'confirmed');``


## Lahe pilt (kui kõik eelnev õnnestus siis peaksid ligipääsema nendele teenustele)

![uhh... Kus on mu pilt?](https://github.com/okane-max/PHP-alused/blob/main/lahepilt.png)


