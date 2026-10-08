# AutoRent Pro - Car Rental Management System

A bootleg car rental platform built with PHP, Bootstrap 5, and MariaDB. Features user registration, car browsing with filters, booking system, and admin panel for reservation management.

---

## Features

### 🚗 User Features
- **User Authentication**: Register and login with email/username
- **Browse Cars**: View all available cars with details (mark, model, engine, fuel, price)
- **Advanced Filtering**: Filter cars by brand, model, fuel type, engine, and max price per day
- **Car Booking**: Reserve cars with date selection and overlap prevention
- **My Reservations**: View active bookings with status tracking
- **Reservation Status**: Track pending (awaiting admin approval) and confirmed bookings

### 👨‍💼 Admin Features
- **Admin Panel**: Secure access for administrators only
- **Car Management**: Add, edit, delete cars in the fleet
- **Reservation Management**: Tabbed interface to view and manage reservations by status
- **Approval Workflow**: Approve pending reservations or cancel any booking
- **Visual Dashboard**: Color-coded reservation cards (pending/confirmed/cancelled)

### 🔒 Security
- Prepared statements for SQL injection prevention
- Password hashing with `password_hash()`
- Session-based authentication with role-based access control
- Atomic transactions with row locking for concurrent booking safety

---

## Tech Stack
- **Backend**: PHP 8.2 with Apache
- **Database**: MariaDB 10.11
- **Frontend**: Bootstrap 5.3.8, vanilla JavaScript
- **Containerization**: Docker & Docker Compose
- **Database Management**: phpMyAdmin

---

## Installation & Setup

### Option 1: Docker (Recommended for Linux/Ubuntu)

1. **Install Docker & Docker Compose**
   ```bash
   apt install docker.io -y
   apt install docker-compose-v2 -y
   ```

2. **Clone the repository**
   ```bash
   cd /var/www/html
   git clone https://github.com/okane-max/PHP-alused.git
   cd PHP-alused
   ```

3. **Start services**
   ```bash
   docker compose up -d
   ```

4. **Access the application**
   - Main site: `http://your-server-ip`
   - phpMyAdmin: `http://your-server-ip:8080`
   - Default DB credentials: User `okane`, Password `okane`

### Option 2: Local Setup (Windows with Docker Desktop)

1. **Install Docker Desktop** for Windows

2. **Clone and navigate**
   ```bash
   git clone https://github.com/okane-max/PHP-alused.git
   cd PHP-alused
   ```

3. **Start with Docker Compose** (PowerShell)
   ```powershell
   docker compose up -d
   ```

4. **Access locally**
   - Main site: `http://localhost`
   - phpMyAdmin: `http://localhost:8080`

---

## Creating an Admin User

After the database initializes, create an admin account:

```bash
docker exec -it autorent_web php /var/www/html/create_admin.php username email@example.com password123
```

Example:
```bash
docker exec -it autorent_web php /var/www/html/create_admin.php admin admin@autorent.com SecurePass123
```

---

## Database Schema

### `users` table
- `id`
- `username` (UNIQUE)
- `email` (UNIQUE)
- `password` (hashed)
- `role` (enum: `client`, `admin`)
- `created_at`

### `cars` table
- `id`
- `mark`, `model`, `engine`, `fuel`
- `price` (daily rental price in €)
- `year`, `transmission`, `seats`
- `description`, `image`, `status` (enum: `vaba`, `rendidud`, `hoolduses`)

### `reservations` table
- `id`
- `user_id` (From → users)
- `car_id` (From → cars)
- `start_date`, `end_date`
- `total_price` (€)
- `status` (enum: `pending`, `confirmed`, `cancelled`)

---

## User Guide

### For Clients

1. **Browse Cars**: Visit the main page to see available cars
2. **Filter**: Use the filter form to search by brand, model, fuel type, engine, or max price
3. **Book a Car**: Click "Rendi" on any car, select start/end dates, and confirm
4. **Check Status**: View your bookings in "Minu broneeringud" - status shows as "pending" until admin approves
5. **Wait for Approval**: Admin reviews and confirms your reservation

### For Admins

1. **Access Admin Panel**: Login as admin and click the admin panel button in the top right
2. **Manage Cars**:
   - Click "Lisa auto" to add a new car
   - Click "Muuda" to edit car details
   - Click "Kustuta" to remove a car
3. **Manage Reservations**:
   - View reservations in three tabs: Pending, Confirmed, Cancelled
   - Click "Kinnita" to approve pending bookings
   - Click "Tühista" to cancel any reservation
4. **View Details**: Each reservation card shows customer info, car details, dates, and total price

---

## Adding More Sample Data

Use phpMyAdmin or direct MySQL:

**Add a user** (password is hashed):
```sql
INSERT INTO `users` (`username`, `password`, `email`, `role`) 
VALUES ('anna_auto', '$2y$10$...', 'anna@auto.ee', 'client');
```

**Add a reservation**:
```sql
INSERT INTO `reservations` (`user_id`, `car_id`, `start_date`, `end_date`, `total_price`, `status`) 
VALUES (2, 9, '2026-10-15', '2026-10-22', 115.50, 'confirmed');
```

## Troubleshooting

### "Autot ei leitud" (No cars found)
- Ensure the database initialized properly: `docker compose logs db`
- Check if `autorent.sql` was imported: Access phpMyAdmin and verify the `cars` table

### Can't login/register
- Ensure the `users` table exists and is accessible
- Check database credentials in `config.php`

### Docker containers not starting
```bash
docker compose down -v    # Remove containers and volumes
docker compose up -d      # Restart from scratch
```

### Database connection error
```bash
docker exec -it autorent_db mysql -u okane -p autorent
# Then enter password: okane
```