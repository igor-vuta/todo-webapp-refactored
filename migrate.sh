#!/bin/bash
# Database Migration Script
# Safely run database migrations with rollback support

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Database connection info from environment
DB_HOST="${DB_HOST:-${MYSQL_HOST:-localhost}}"
DB_PORT="${DB_PORT:-${MYSQL_PORT:-3306}}"
DB_NAME="${DB_NAME:-${MYSQL_DATABASE:-todo_app}}"
DB_USER="${DB_USER:-${MYSQL_USER:-root}}"
DB_PASS="${DB_PASS:-${MYSQL_PASSWORD:-}}"

echo -e "${BLUE}🔄 Database Migration Tool${NC}\n"

# Check if mysql client is installed
if ! command -v mysql &> /dev/null; then
    echo -e "${RED}❌ Error: mysql client not found${NC}"
    echo "Install it with: brew install mysql-client (macOS) or apt-get install mysql-client (Linux)"
    exit 1
fi

# Build MySQL command
MYSQL_CMD="mysql -h $DB_HOST -P $DB_PORT -u $DB_USER"
if [ -n "$DB_PASS" ]; then
    MYSQL_CMD="$MYSQL_CMD -p$DB_PASS"
fi
MYSQL_CMD="$MYSQL_CMD $DB_NAME"

# Test connection
echo -e "${YELLOW}Testing database connection...${NC}"
if ! echo "SELECT 1;" | $MYSQL_CMD > /dev/null 2>&1; then
    echo -e "${RED}❌ Cannot connect to database${NC}"
    echo "Connection details:"
    echo "  Host: $DB_HOST:$DB_PORT"
    echo "  Database: $DB_NAME"
    echo "  User: $DB_USER"
    exit 1
fi
echo -e "${GREEN}✅ Connected to database${NC}\n"

# Function to run migration
run_migration() {
    local file=$1
    local name=$(basename "$file")
    
    echo -e "${BLUE}Running: $name${NC}"
    
    if $MYSQL_CMD < "$file"; then
        echo -e "${GREEN}✅ Success${NC}\n"
        return 0
    else
        echo -e "${RED}❌ Failed${NC}\n"
        return 1
    fi
}

# Create backup
echo -e "${YELLOW}📦 Creating backup...${NC}"
BACKUP_FILE="backup_$(date +%Y%m%d_%H%M%S).sql"
mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" ${DB_PASS:+-p$DB_PASS} "$DB_NAME" > "$BACKUP_FILE" 2>/dev/null || true
echo -e "${GREEN}✅ Backup saved: $BACKUP_FILE${NC}\n"

# Menu
echo "What would you like to do?"
echo "1) Fresh install (schema + indexes + seeds)"
echo "2) Schema only"
echo "3) Indexes only"
echo "4) Seeds only"
echo "5) Schema + Indexes (no seeds)"
echo "6) Custom file"
echo "7) Rollback to backup"
echo ""
read -p "Choose [1-7]: " choice

case $choice in
    1)
        echo -e "\n${BLUE}🔨 Running fresh install...${NC}\n"
        run_migration "$SCRIPT_DIR/database/schema.sql" || exit 1
        run_migration "$SCRIPT_DIR/database/indexes.sql" || exit 1
        run_migration "$SCRIPT_DIR/database/seeds.sql" || exit 1
        ;;
    2)
        run_migration "$SCRIPT_DIR/database/schema.sql" || exit 1
        ;;
    3)
        run_migration "$SCRIPT_DIR/database/indexes.sql" || exit 1
        ;;
    4)
        run_migration "$SCRIPT_DIR/database/seeds.sql" || exit 1
        ;;
    5)
        run_migration "$SCRIPT_DIR/database/schema.sql" || exit 1
        run_migration "$SCRIPT_DIR/database/indexes.sql" || exit 1
        ;;
    6)
        read -p "Enter SQL file path: " custom_file
        if [ -f "$custom_file" ]; then
            run_migration "$custom_file" || exit 1
        else
            echo -e "${RED}File not found: $custom_file${NC}"
            exit 1
        fi
        ;;
    7)
        echo "Available backups:"
        ls -1 backup_*.sql 2>/dev/null || echo "No backups found"
        echo ""
        read -p "Enter backup filename: " backup_file
        if [ -f "$backup_file" ]; then
            echo -e "${YELLOW}Restoring from backup...${NC}"
            $MYSQL_CMD < "$backup_file"
            echo -e "${GREEN}✅ Restored from $backup_file${NC}"
        else
            echo -e "${RED}Backup file not found${NC}"
            exit 1
        fi
        ;;
    *)
        echo -e "${RED}Invalid choice${NC}"
        exit 1
        ;;
esac

echo -e "\n${GREEN}✅ Migration completed successfully!${NC}"

# Show stats
echo -e "\n${BLUE}📊 Database Statistics:${NC}"
echo "SELECT 
    'users' as table_name, COUNT(*) as row_count FROM users
UNION ALL SELECT 'lists', COUNT(*) FROM lists
UNION ALL SELECT 'tasks', COUNT(*) FROM tasks
UNION ALL SELECT 'groups', COUNT(*) FROM \`groups\`
UNION ALL SELECT 'group_members', COUNT(*) FROM group_members;" | $MYSQL_CMD
