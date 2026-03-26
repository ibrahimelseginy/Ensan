import sqlite3
import os

db_path = r'f:\Enasn\database\database.sqlite'

if not os.path.exists(db_path):
    print(f"Database not found at {db_path}")
    exit(1)

conn = sqlite3.connect(db_path)
cursor = conn.cursor()

try:
    # Add columns to tasks table
    print("Adding rating column to tasks table...")
    try:
        cursor.execute("ALTER TABLE tasks ADD COLUMN rating INTEGER")
        print("rating column added.")
    except sqlite3.OperationalError as e:
        if "duplicate column name" in str(e):
            print("rating column already exists.")
        else:
            raise e

    print("Adding evaluation_notes column to tasks table...")
    try:
        cursor.execute("ALTER TABLE tasks ADD COLUMN evaluation_notes TEXT")
        print("evaluation_notes column added.")
    except sqlite3.OperationalError as e:
        if "duplicate column name" in str(e):
            print("evaluation_notes column already exists.")
        else:
            raise e

    # Register migration
    migration_name = '2025_12_07_000008_add_evaluation_fields_to_tasks_table'
    cursor.execute("SELECT id FROM migrations WHERE migration = ?", (migration_name,))
    if cursor.fetchone() is None:
        cursor.execute("INSERT INTO migrations (migration, batch) VALUES (?, ?)", (migration_name, 1))
        print(f"Migration {migration_name} registered.")
    else:
        print(f"Migration {migration_name} already registered.")

    conn.commit()
    print("Migration completed successfully.")

except Exception as e:
    print(f"Error: {e}")
    conn.rollback()
finally:
    conn.close()
