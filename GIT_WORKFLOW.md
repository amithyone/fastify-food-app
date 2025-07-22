# 🔄 Git Workflow Guide

## ✅ **Your Repository is Now Updated!**

Your latest changes have been successfully pushed to:
**https://github.com/amithyone/fastify-food-app**

## 🚀 **How to Keep Your Repository Updated**

### **Method 1: Automated Script (Recommended)**

```bash
# Run the automated update script
./update-repo.sh
```

This script will:
- ✅ Check for changes
- ✅ Ask for a commit message
- ✅ Add all changes
- ✅ Commit and push automatically

### **Method 2: Manual Commands**

```bash
# 1. Check what changed
git status

# 2. Add all changes
git add .

# 3. Commit with a message
git commit -m "Your update description"

# 4. Push to GitHub
git push origin main
```

### **Method 3: Quick One-Liner**

```bash
# Add, commit, and push in one command
git add . && git commit -m "Update" && git push origin main
```

## 📋 **Daily Workflow**

### **When You Make Changes:**

1. **Edit your files** (add features, fix bugs, etc.)
2. **Test your changes** locally
3. **Update repository** using any method above
4. **Deploy to server** (optional): `./deploy-workflow.sh deploy`

### **Example Workflow:**

```bash
# 1. Make changes to your code
# 2. Test locally
php artisan serve

# 3. Update repository
./update-repo.sh

# 4. Deploy to server (if ready)
./deploy-workflow.sh deploy
```

## 🔍 **Useful Git Commands**

### **Check Status**
```bash
git status                    # See what changed
git diff                      # See detailed changes
git log --oneline -5         # See last 5 commits
```

### **Branch Management**
```bash
git branch                    # List branches
git checkout -b feature-name  # Create new branch
git checkout main            # Switch to main branch
git merge feature-name       # Merge branch
```

### **Undo Changes**
```bash
git restore <file>           # Undo changes in a file
git reset --hard HEAD        # Undo all changes (careful!)
git revert <commit-hash>     # Undo a specific commit
```

## 🌐 **Repository Information**

- **URL**: https://github.com/amithyone/fastify-food-app
- **Branch**: `main`
- **Remote**: `origin`
- **Status**: Public repository

## 🚨 **Important Notes**

### **Before Pushing:**
- ✅ Test your changes locally
- ✅ Make sure the app still works
- ✅ Check for any sensitive data (API keys, passwords)

### **Best Practices:**
- 📝 Write clear commit messages
- 🔄 Push regularly (don't let changes pile up)
- 🧪 Test before pushing
- 📋 Use the automated script for consistency

## 🆘 **Troubleshooting**

### **If Push Fails:**
```bash
# Pull latest changes first
git pull origin main

# Then try pushing again
git push origin main
```

### **If You Made a Mistake:**
```bash
# Undo last commit (but keep changes)
git reset --soft HEAD~1

# Undo last commit (and discard changes)
git reset --hard HEAD~1
```

### **Check Remote Status:**
```bash
git remote -v              # See remote URLs
git fetch origin           # Get latest info
git log origin/main..HEAD  # See local commits not on remote
```

## 🎯 **Quick Reference**

| Action | Command |
|--------|---------|
| Check status | `git status` |
| Add changes | `git add .` |
| Commit | `git commit -m "message"` |
| Push | `git push origin main` |
| Pull updates | `git pull origin main` |
| Automated update | `./update-repo.sh` |
| Deploy | `./deploy-workflow.sh deploy` |

Your Fastify application is now fully version controlled and ready for continuous development! 🚀 