#!/bin/bash
# Deploy to Fly.io
# Requires: fly CLI installed (curl -L https://fly.io/install.sh | sh)

set -e

GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${BLUE}✈️  ToDo WebApp - Fly.io Deployment${NC}\n"

# Check if fly CLI is installed
if ! command -v fly &> /dev/null; then
    echo -e "${YELLOW}Installing Fly CLI...${NC}"
    curl -L https://fly.io/install.sh | sh
    export PATH="$HOME/.fly/bin:$PATH"
fi

# Login to Fly.io
echo -e "\n${BLUE}📝 Step 1: Login to Fly.io${NC}"
fly auth login

# Launch app (creates fly.toml if not exists)
echo -e "\n${BLUE}🚀 Step 2: Launch application${NC}"
if [ ! -f "fly.toml" ]; then
    fly launch --no-deploy
else
    echo "fly.toml already exists, skipping launch"
fi

# Create PostgreSQL database (MySQL not directly supported on Fly)
echo -e "\n${BLUE}🗄️  Step 3: Create database${NC}"
read -p "Create a new Postgres database? (y/N) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    fly postgres create --name todo-webapp-db
    fly postgres attach todo-webapp-db
fi

# Generate and set secrets
echo -e "\n${BLUE}🔐 Step 4: Set secrets${NC}"
JWT_SECRET=$(openssl rand -base64 32)
echo -e "Generated JWT_SECRET: ${GREEN}${JWT_SECRET}${NC}"

fly secrets set JWT_SECRET="$JWT_SECRET"
fly secrets set APP_ENV="production"

# Deploy
echo -e "\n${BLUE}🚢 Step 5: Deploy${NC}"
fly deploy

# Initialize database
echo -e "\n${BLUE}📊 Step 6: Initialize database${NC}"
echo "Run these commands to initialize your database:"
echo ""
echo "fly ssh console"
echo "mysql < database/schema.sql"
echo "mysql < database/seeds.sql"
echo "mysql < database/indexes.sql"

echo -e "\n${GREEN}✅ Deployment complete!${NC}"
fly open
