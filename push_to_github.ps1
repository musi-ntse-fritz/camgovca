Write-Host "========================================" -ForegroundColor Green
Write-Host "CamGovCA GitHub Push Script" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""

$githubUsername = Read-Host "Please enter your GitHub username"

Write-Host ""
Write-Host "Adding remote repository..." -ForegroundColor Yellow
git remote add origin "https://github.com/$githubUsername/camgovca.git"

Write-Host ""
Write-Host "Pushing to GitHub..." -ForegroundColor Yellow
git push -u origin main

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "Push completed!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Your repository is now available at:" -ForegroundColor Cyan
Write-Host "https://github.com/$githubUsername/camgovca" -ForegroundColor Cyan
Write-Host ""
Read-Host "Press Enter to continue" 