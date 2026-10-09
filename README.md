# AutoRent Pro - Autorendi haldussüsteem

Lihtne autorendi platvorm, mis on ehitatud kasutades PHP-d, Bootstrap 5 ja MariaDB-d. Sisaldab kasutajate registreerimist, filtritega autofunktsiooni sirvimist, broneerimissüsteemi ja administraatori paneeli broneeringute haldamiseks.

---

## Funktsioonid

### 🚗 Kasutaja funktsioonid
- **Kasutaja autentimine**: Registreerumine ja sisselogimine e-posti/kasutajanimega
- **Autode sirvimine**: Kõikide saadaval olevate autode vaatamine koos üksikasjadega (mark, mudel, mootor, kütus, hind)
- **Täpsem filtreerimine**: Autode filtreerimine brändi, mudeli, kütusetüübi, mootori ja maksimaalse päevahinna järgi
- **Auto broneerimine**: Autode reserveerimine koos kuupäeva valikuga ja kattuvuste ennetamisega
- **Minu broneeringud**: Aktiivsete broneeringute vaatamine koos staatuse jälgimisega
- **Broneeringu staatus**: Ootel (administraatori heakskiitu ootavate) ja kinnitatud broneeringute jälgimine

### 👨‍💼 Administraatori funktsioonid
- **Admini paneel**: Turvaline juurdepääs ainult administraatorile
- **Autode haldamine**: Autode lisamine, muutmine ja kustutamine autode andmebaasis
- **Broneeringute haldamine**: akkidega liides broneeringute vaatamiseks ja haldamiseks staatuse järgi
- **Broneeringute vastuvõtt**: Ootel broneeringute kinnitamine või mis tahes broneeringu tühistamine
- **Visuaalne töölaud**: Värvikoodidega broneeringukaardid (pending/confirmed/cancelled)

### 🔒 Turvalisus
- Ettevalmistatud päringud (prepared statements) SQL-süsteemi rünnete (SQL injection) vältimiseks
- Paroolide räsimine funktsiooniga `password_hash()`
- Sessioonipõhine autentimine koos rollipõhise juurdepääsu kontrolliga
- Aatomilised transaktsioonid koos ridade lukustamisega samaaegsete broneeringute turvalisuse tagamiseks

---

## Paigaldamine ja seadistamine

### Variant 1: Docker (Soovitatav Linux/Ubuntu puhul)

1. **Paigalda Docker & Docker Compose**
   ```bash
   apt install docker.io -y
   apt install docker-compose-v2 -y
   ```

2. **Klooni hoidla (repository)**
   ```bash
   cd /var/www/html
   git clone https://github.com/okane-max/PHP-alused.git
   cd PHP-alused
   ```

3. **Käivita teenused**
   ```bash
   docker compose up -d
   ```

4. **Ava veebilehed**
   - Pealeht: `http://sinu-serveri-ip`
   - phpMyAdmin: `http://sinu-serveri-ip:8080`
   - Andmebaasi vaikeandmed: Kasutaja `okane`, Parool `okane`

### Variant 2: Kohalik seadistus (Windows koos Docker Desktopiga)

1. **Paigalda Docker Desktop** Windowsi jaoks

2. **klooni ja liigu kausta**
   ```bash
   git clone https://github.com/okane-max/PHP-alused.git
   cd PHP-alused
   ```

3. **Käivita Docker Compose abil** (PowerShell)
   ```powershell
   docker compose up -d
   ```

4. **Ava kohalikult**
   - Pealeht: `http://localhost`
   - phpMyAdmin: `http://localhost:8080`

---

## Admin-kasutaja loomine

Pärast andmebaasi käivitumist loo administraatori konto:

```bash
docker exec -it autorent_web php /var/www/html/create_admin.php kasutajanimi email@lahekoht.ee misiganesparoolteidkutsub
```

Näide:
```bash
docker exec -it autorent_web php /var/www/html/create_admin.php admin admin@auto.ee SuurjaKuriPar00l
```

---

## Andmebaasi struktuur (Schema)

### `users` table
- `id`
- `username` (unikaalne)
- `email` (unikaalne)
- `password` (räsitud)
- `role` (valikud: `client`, `admin`)
- `created_at`

### `cars` table
- `id`
- `mark`, `model`, `engine`, `fuel`
- `price` (rendihind päevakohta eurodes)
- `year`, `transmission`, `seats`
- `description`, `image`, `status` (valikud: `vaba`, `rendidud`, `hoolduses`)

### `reservations` table
- `id`
- `user_id` (pärineb → users)
- `car_id` (pärineb → cars)
- `start_date`, `end_date`
- `total_price` (€)
- `status` (valikud: `pending`, `confirmed`, `cancelled`)

---

## Kasutusjuhend

### Klientidele

1. **Autode sirvimine**: Külasta pealehte, et näha saadaval olevaid autosid.
2. **Filtreerimine**: Kasuta filtri vormi, et otsida marki, mudelit, kütusetüüpi, mootorit või maksimaalset hinda.
3. **Auto broneerimine**: Klõpsa soovitud autol nupule "Rendi", vali algus- ja lõpukuupäev ning kinnita.
4. **Staatuse kontrollimine**: Vaata oma broneeringuid jaotisest "Minu broneeringud" – staatus on "pending" (ootel) kuni administraatori heakskiiduni.
5. **Heakskiidu ootamine**: Administraator vaatab broneeringu üle ja kinnitab selle.

### Administraatoritele

1. **Admini paneeli sisenemine**: Logi sisse administraatorina ja klõpsa üleval paremas nurgas olevat admini paneeli nuppu.
2. **Autode haldamine**:
	- Uue auto lisamiseks klõpsa "Lisa auto".
	- Auto andmete muutmiseks klõpsa "Muuda".
	- Auto eemaldamiseks klõpsa "Kustuta".
3. **Broneeringute haldamine**:
	- Vaata broneeringuid kolmel vahekaardil: Ootel (Pending), Kinnitatud (Confirmed), Tühistatud (Cancelled).
	- Ootel broneeringute heakskiitmiseks klõpsa "Kinnita".
	- Mis tahes broneeringu tühistamiseks klõpsa "Tühista".
4. **Andmete vaatamine**: Iga broneeringukaart näitab kliendi infot, auto andmeid, kuupäevi ja koguhinda.

---

## Näidisandmete lisamine

Kasuta phpMyAdmini või otse MySQL-i:

**Kasutaja lisamine** (parool on räsitud):
```sql
INSERT INTO `users` (`username`, `password`, `email`, `role`) 
VALUES ('anna_auto', '$2y$10$...', 'anna@auto.ee', 'client');
```

**Broneeringu lisamine**:
```sql
INSERT INTO `reservations` (`user_id`, `car_id`, `start_date`, `end_date`, `total_price`, `status`) 
VALUES (2, 9, '2026-10-15', '2026-10-22', 115.50, 'confirmed');
```

## Tõrkeotsing

### "Autot ei leitud"
- Veendu, et andmebaas käivitus korrektselt: `docker compose logs db`
- Kontrolli, kas fail `autorent.sql` imporditi: Ava phpMyAdmin ja kontrolli tabelit `cars`

### Sisselogimine/Registreerumine ei tööta
- Veendu, et tabel `users` on olemas ja ligipääsetav
- Kontrolli andmebaasi ühenduse andmeid failis `config.php`

### Dockeri konteinerid ei käivitu
```bash
docker compose down -v
docker compose up -d
```

### Andmebaasi ühenduse viga
```bash
docker exec -it autorent_db mysql -u okane -p autorent
# Seejärel sisesta parool: okane
```
