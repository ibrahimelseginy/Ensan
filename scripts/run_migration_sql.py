import sqlite3
import os

db_path = os.path.join("database", "database.sqlite")
print(f"Connecting to database at {db_path}")

try:
    conn = sqlite3.connect(db_path)
    cursor = conn.cursor()

    sqls = [
        "ALTER TABLE employee_attendances ADD COLUMN rating INTEGER DEFAULT NULL",
        "ALTER TABLE employee_attendances ADD COLUMN evaluation_notes TEXT DEFAULT NULL",
        "ALTER TABLE volunteer_attendances ADD COLUMN rating INTEGER DEFAULT NULL",
        "ALTER TABLE volunteer_attendances ADD COLUMN evaluation_notes TEXT DEFAULT NULL"
    ]

    # Register migration to avoid re-running if they ever fix php
    migration_name = "2025_12_07_000007_add_evaluation_fields_to_attendances_tables"
    insert_migration_sql = "INSERT INTO migrations (migration, batch) VALUES (?, ?)"

    for sql in sqls:
        try:
            cursor.execute(sql)
            print(f"Executed: {sql}")
        except Exception as e:
            print(f"Error executing {sql}: {e}")

    try:
        # get max batch
        cursor.execute("SELECT MAX(batch) FROM migrations")
        row = cursor.fetchone()
        batch = 1
        if row and row[0]:
            batch = row[0] + 1
            
        cursor.execute(insert_migration_sql, (migration_name, batch))
        print(f"Registered migration: {migration_name}")
    except Exception as e:
        print(f"Error registering migration: {e}")

    conn.commit()
    conn.close()
    print("Done.")

except Exception as e:
    print(f"Database error: {e}")
