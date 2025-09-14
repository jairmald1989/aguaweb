# Database Update Required

To add the Cédula/RUC field to the system, you need to execute the following SQL command on your database:

```sql
ALTER TABLE owners ADD COLUMN cedula_ruc VARCHAR(20) DEFAULT '' AFTER contact;
```

This will add a new column `cedula_ruc` to the `owners` table with a maximum length of 20 characters, allowing for both cédulas and RUC numbers. The column will be added after the `contact` column.

## Notes:
- The field allows up to 20 characters to accommodate different ID formats
- Default value is empty string for existing records
- The column is positioned after the contact field for logical ordering