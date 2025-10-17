<!-- 982c886e-891b-4427-85d4-8fc10af50992 cef0d11d-8fef-49e2-ad74-568eafea4892 -->
# Start the Food Ordering system on Windows/WAMP

#### 1) Prereqs

- Install and start WampServer (Apache + MySQL) and ensure both services are green.
- Place the project folder under `C:\wamp64\www\` (already there). Optional: rename folder to `food-ordering` (avoid spaces for cleaner URLs).

#### 2) Create database

- Open `http://localhost/phpmyadmin`.
- Create a database named `food` with collation `utf8mb4_general_ci`.

#### 3) Import schema and seed data

- In phpMyAdmin, select `food` → Import → choose `account/sql/food.sql` and run.
- If you see errors, try the root `food.sql` as an alternative import file.

#### 4) Configure DB connection (if needed)

- Check `account/includes/connect.php` and set host/user/pass and database name to match your MySQL setup (user `root`, empty password on WAMP by default; DB `food`).
```3:12:account/includes/connect.php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "food_db"; // change to 'food' if your DB is named 'food'

$con = mysqli_connect($host, $user, $pass, $dbname);
```

- If your database is named `food`, change `$dbname` to `"food"`.

#### 5) Launch in browser

- If you kept the folder as `food ordering`:
  - Open `http://localhost/food%20ordering/account/login.php` (or `index.php`).
- If renamed to `food-ordering`:
  - Open `http://localhost/food-ordering/account/login.php`.

#### 6) Login

- Admin credentials from the included README (may vary by dataset):
  - Username: `admin`
  - Password: `Demopass@123`
- Or check the `users` table in phpMyAdmin and log in with any user/password shown there.

#### 7) Quick verification

- As Admin: visit `account/admin-page.php` to manage items/users/orders.
- As Customer: add items to cart and try a checkout (use Wallet/COD per dataset notes).

#### 8) Common issues

- mysqli connect error: verify MySQL is running, DB `food` exists, and `connect.php` creds match.
- 404 or blank page: verify Apache is running and URL path matches folder name.
- SQL import errors: re-import using the other `food.sql` file; ensure DB is empty before import.

### To-dos

- [ ] Create MySQL database `food` in phpMyAdmin
- [ ] Import schema/data from `account/sql/food.sql`
- [ ] Set DB creds and name in `account/includes/connect.php`
- [ ] Open login page in browser via localhost URL
- [ ] Log in with admin or sample user from DB