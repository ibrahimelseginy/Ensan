import sqlite3
import os
import time

db_path = os.path.join("database", "database.sqlite")
print(f"Connecting to database at {db_path}")

try:
    conn = sqlite3.connect(db_path)
    cursor = conn.cursor()

    # Create table
    create_sql = """
    CREATE TABLE IF NOT EXISTS "change_requests" (
        "id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        "user_id" INTEGER NOT NULL,
        "model_type" VARCHAR NOT NULL,
        "model_id" INTEGER,
        "action" VARCHAR NOT NULL,
        "payload" TEXT,
        "status" VARCHAR NOT NULL DEFAULT 'pending',
        "reviewer_id" INTEGER,
        "rejection_reason" TEXT,
        "created_at" DATETIME,
        "updated_at" DATETIME,
        FOREIGN KEY("user_id") REFERENCES "users"("id") ON DELETE CASCADE,
        FOREIGN KEY("reviewer_id") REFERENCES "users"("id")
    );
    """
    
    cursor.execute(create_sql)
    print("Table 'change_requests' created or already exists.")

    # Register migration
    migration_name = "2025_12_15_030000_create_change_requests_table"
    
    # Check if migration exists
    cursor.execute("SELECT migration FROM migrations WHERE migration = ?", (migration_name,))
    if cursor.fetchone():
        print("Migration already registered.")
    else:
        # Get max batch
        cursor.execute("SELECT MAX(batch) FROM migrations")
        row = cursor.fetchone()
        batch = 1
        if row and row[0]:
            batch = row[0] + 1
            
        cursor.execute("INSERT INTO migrations (migration, batch) VALUES (?, ?)", (migration_name, batch))
        print(f"Registered migration: {migration_name}")

    conn.commit()
    conn.close()
    print("Migration completed successfully.")

except Exception as e:
    print(f"Error: {e}")
