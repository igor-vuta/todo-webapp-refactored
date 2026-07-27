#!/bin/bash

# Color codes for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}🚀 ToDo WebApp Deployment Helper${NC}\n"

# Check if Railway CLI is installed
if ! command -v railway &> /dev/null; then
    echo -e "${YELLOW}⚠️  Railway CLI not found. Installing...${NC}"
    npm install -g @railway/cli
fi

# Login to Railway
echo -e "\n${BLUE}📝 Step 1: Login to Railway${NC}"
railway login

# Initialize project
echo -e "\n${BLUE}📦 Step 2: Initialize Railway project${NC}"
railway init

# Add MySQL database
echo -e "\n${BLUE}🗄️  Step 3: Add MySQL database${NC}"
echo "Run this command manually after the script:"
echo "  railway add --database mysql"
echo ""
read -p "Press enter once you've added MySQL database..."

# Generate JWT secret
echo -e "\n${BLUE}🔐 Step 4: Generate JWT Secret${NC}"
JWT_SECRET=$(openssl rand -base64 32)
echo -e "Generated JWT_SECRET: ${GREEN}${JWT_SECRET}${NC}"

# Set environment variables
echo -e "\n${BLUE}⚙️  Step 5: Set environment variables${NC}"
railway variables set JWT_SECRET="$JWT_SECRET"

# Deploy
echo -e "\n${BLUE}🚢 Step 6: Deploy to Railway${NC}"
railway up

# Get database connection info
echo -e "\n${BLUE}📊 Step 7: Initialize database${NC}"
echo "Run these commands to initialize your database:"
echo ""
echo "railway run bash -c \"mysql -h \\\$MYSQL_HOST -P \\\$MYSQL_PORT -u \\\$MYSQL_USER -p\\\$MYSQL_PASSWORD \\\$MYSQL_DATABASE < database/schema.sql\""
echo "railway run bash -c \"mysql -h \\\$MYSQL_HOST -P \\\$MYSQL_PORT -u \\\$MYSQL_USER -p\\\$MYSQL_PASSWORD \\\$MYSQL_DATABASE < database/seeds.sql\""
echo "railway run bash -c \"mysql -h \\\$MYSQL_HOST -P \\\$MYSQL_PORT -u \\\$MYSQL_USER -p\\\$MYSQL_PASSWORD \\\$MYSQL_DATABASE < database/indexes.sql\""

echo -e "\n${GREEN}✅ Deployment complete!${NC}"
echo -e "\n${BLUE}🌐 Your app is live at:${NC}"
railway open
