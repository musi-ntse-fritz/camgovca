@echo off
echo ========================================
echo CamGovCA GitHub Push Script
echo ========================================
echo.

echo Please enter your GitHub username:
set /p GITHUB_USERNAME=

echo.
echo Adding remote repository...
git remote add origin https://github.com/%GITHUB_USERNAME%/camgovca.git

echo.
echo Pushing to GitHub...
git push -u origin main

echo.
echo ========================================
echo Push completed!
echo ========================================
echo.
echo Your repository is now available at:
echo https://github.com/%GITHUB_USERNAME%/camgovca
echo.
pause 