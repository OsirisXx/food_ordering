<!-- 5dec0c62-61d9-4bc6-bd3e-825d16e6821c e72e6082-830d-4086-9b95-af4775670a12 -->
# Implement Functional Backup Tab

## Questions (please confirm)
1. Should backup include only the MySQL database, or also site assets (images/css/js) into a zip?
   - a) Database only
   - b) Database + assets (zip)
2. Do you want a one-click restore from a chosen backup in the UI?
   - a) Not now
   - b) Yes, add restore

## Scope (assuming 1a and 2a)
- Database-only backups generated on demand from the Backup tab.
- Store backups under `account/backups/` as timestamped `.sql` files.
- List existing backups with Download and Delete actions.

## Files/Changes
- `account/utilities-admin.php`
  - Wire the existing Backup button to call a new router for DB export and show success/error messages.
  - Render a table under the Backup card that lists files in `account/backups/` with Download/Delete buttons.
- `account/routers/backup-database.php` (new)
  - Generate SQL dump of all tables in the current database (schema + data), using mysqli and proper escaping.
  - Save to `account/backups/backup_YYYYMMDD_HHMMSS.sql` and return JSON `{ success, message, file }`.
- `account/routers/delete-backup.php` (new)
  - Validate filename, delete selected backup, return JSON `{ success, message }`.
- `account/backups/` (new directory)
  - Ensure writable by PHP process.

## Implementation Details
- Detect DB name from the active mysqli connection (`SELECT DATABASE()`), enumerate tables via `SHOW TABLES`.
- For each table: `SHOW CREATE TABLE` for DDL, then `SELECT *` batching rows and building INSERTs with proper escaping; handle BLOBs with hex encoding (`0x...`).
- Use `Content-Disposition` only for the JSON-triggered download link; main action writes to disk and returns filename.
- Client-side (Backup tab) calls routers via AJAX to avoid full-page reload; updates the list on success.

## Safety/Validation
- Only allow operations within `account/backups/`; reject paths with `..`.
- Limit list to `.sql` files; show size and created time.

## Follow-ups (optional)
- Optionally zip backups and/or include assets.
- Optional restore UI (read and run SQL; requires careful safety checks).

### To-dos

- [ ] Create routers/backup-database.php to dump DB to backups/
- [ ] Create routers/delete-backup.php to remove a backup file
- [ ] Hook Backup button to router; list backups with actions
- [ ] Create account/backups/ with write checks