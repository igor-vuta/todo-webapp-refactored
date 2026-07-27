#!/bin/bash
# Deploy to Render.com
# Uses render.yaml for infrastructure as code

set -e

GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${BLUE}🎨 ToDo WebApp - Render Deployment${NC}\n"

# Generate JWT secret
echo -e "${BLUE}🔐 Generating JWT Secret${NC}"
JWT_SECRET=$(openssl rand -base64 32)
echo -e "Generated JWT_SECRET: ${GREEN}${JWT_SECRET}${NC}"
echo ""
echo "Save this secret! You'll need to add it in Render dashboard."
echo ""

# Instructions
echo -e "${BLUE}📋 Deployment Steps:${NC}"
echo ""
echo "1. Go to https://render.com"
echo "2. Click 'New' → 'Blueprint'"
echo "3. Connect your GitHub repository"
echo "4. Render will detect render.yaml"
echo "5. In the dashboard, set environment variables:"
echo "   - JWT_SECRET=$JWT_SECRET"
echo "   - CORS_ORIGINS=https://your-frontend-url.onrender.com"
echo ""
echo "6. Deploy! Render will:"
echo "   - Create MySQL database"
echo "   - Deploy PHP backend"
echo "   - Deploy static frontend"
echo ""
echo "7. Initialize database:"
echo "   - Open the database shell in Render"
echo "   - Run: source database/schema.sql"
echo "   - Run: source database/seeds.sql"
echo "   - Run: source database/indexes.sql"
echo ""
echo -e "${GREEN}✅ Follow these steps in Render dashboard${NC}"
echo ""
echo "Render free tier includes:"
echo "  - 750 hours/month of runtime"
echo "  - Auto-suspend after 15 min inactivity"
echo "  - 100GB bandwidth"
