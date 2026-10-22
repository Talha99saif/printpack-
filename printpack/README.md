# PrintPack — Online Printing & Packaging Services

A modular Core PHP 8.1+ / MySQL 8 starter application for customers, vendors, and administrators. The UI is responsive and follows the PrintPack reference direction: mint hero, dark-green accents, product mockups, category tiles, and a compact trust-feature strip.

## Included
- Role-aware registration/login (customer/vendor/admin), password hashing, session regeneration, CSRF helpers.
- Service catalogue with keyword/category search, vendor/service management foundations.
- Multi-vendor cart and checkout (one customer checkout creates vendor-specific orders), configurable quantity-based pricing and distance-based delivery estimate.
- Design upload validation (PDF/JPG/PNG/WEBP, 15 MB), private-ish storage location, randomized filenames.
- Order statuses, customer order history, vendor order handling, notifications, reviews, and admin reporting foundations.
- COD as default payment method; optional Stripe Checkout integration hook (keys must be configured).
- Google Maps link-based route preview and distance estimate hook. For live courier GPS, connect a driver/mobile location feed; a static map cannot provide live tracking by itself.
- SQL schema, seed data, environment config, Postman collection, test checklist, and incremental Git commits.

## Requirements
PHP 8.1+ with `pdo_mysql`, `fileinfo`, `mbstring`; MySQL 8/MariaDB; XAMPP or Apache/Nginx. Optional: Composer, Stripe account, Google Maps API key.

## Run in XAMPP

The `public/.htaccess` file enables clean routes such as `/printpack/public/login`. In XAMPP, make sure Apache has `mod_rewrite` enabled and the `htdocs` directory allows `.htaccess` overrides. If links still show 404, restart Apache after checking `httpd.conf` (`LoadModule rewrite_module modules/mod_rewrite.so`) and `AllowOverride All`.
1. Copy `printpack` into `C:\xampp\htdocs\printpack`.
2. Start Apache and MySQL.
3. Import `database/schema.sql` using phpMyAdmin.
4. Copy `.env.example` to `.env` and set database credentials. This project uses a tiny `.env` loader (no Composer needed).
5. Visit `http://localhost/printpack/public/`.
6. Create a user via Register. To promote an account to admin locally, run:
   ```sql
   UPDATE users SET role='admin', status='active' WHERE email='you@example.com';
   ```
7. Ensure `public/uploads/designs` is writable by PHP. In production, move uploads outside the public web root and serve them through an authenticated download controller.

## Git history
The repository is intentionally split into readable commits. Run `git log --oneline` to follow the build sequence.

## Important production work
This is a professional learning/MVP foundation, not a certified production deployment. Before real payments or public launch: configure HTTPS, secure cookies, rate limiting, email/SMS provider, virus scanning for uploads, private upload delivery, real geocoding/routing, webhook signature verification, transactional outbox/retries, audit logs, backups, and a deployment-specific security review. Stripe is disabled unless valid keys and a verified webhook flow are configured. COD works without an online gateway.
