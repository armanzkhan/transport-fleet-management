#!/bin/bash

# 🚀 GitHub Repository Setup Script
# Run this script to prepare your repository for deployment

echo "🚀 Setting up GitHub repository for Transport Fleet Management..."
echo "================================================================"

# Check if git is initialized
if [ ! -d ".git" ]; then
    echo "📦 Initializing Git repository..."
    git init
else
    echo "✅ Git repository already initialized"
fi

# Add all files
echo "📁 Adding all files to Git..."
git add .

# Commit changes
echo "💾 Committing changes..."
git commit -m "Initial commit: Transport Fleet Management System

✅ Complete Laravel application
✅ All 18 SRS modules implemented
✅ Role-based access control
✅ Bilingual support (English/Urdu)
✅ Export/Print functionality
✅ Performance optimized
✅ Deployment ready

Features:
- Admin, Fleet, Finance dashboards
- User management
- Vehicle management
- Journey vouchers
- Cash book management
- 12+ comprehensive reports
- Smart suggestions
- Keyboard navigation
- Developer access management
- Notifications system

Ready for deployment to Railway/Render!"

echo "✅ Git repository prepared!"
echo ""
echo "📋 Next steps:"
echo "1. Create GitHub repository:"
echo "   - Go to https://github.com/new"
echo "   - Name: transport-fleet-management"
echo "   - Make it Public"
echo "   - Don't initialize with README"
echo ""
echo "2. Connect local repository:"
echo "   git remote add origin https://github.com/YOURUSERNAME/transport-fleet-management.git"
echo "   git branch -M main"
echo "   git push -u origin main"
echo ""
echo "3. Deploy to Railway:"
echo "   - Go to https://railway.app"
echo "   - Connect GitHub"
echo "   - Deploy from repository"
echo "   - Add MySQL database"
echo "   - Set environment variables"
echo "   - Deploy!"
echo ""
echo "🎉 Your Transport Fleet Management System is ready for deployment! 🚛✨"
