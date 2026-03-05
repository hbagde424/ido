@echo off
echo ================================================
echo Creating Deployment Package for Live Server
echo ================================================
echo.

REM Create deployment folder
if not exist "deployment_package" mkdir deployment_package
if not exist "deployment_package\Modules\Essentials\Entities" mkdir deployment_package\Modules\Essentials\Entities
if not exist "deployment_package\Modules\Essentials\Http\Controllers" mkdir deployment_package\Modules\Essentials\Http\Controllers
if not exist "deployment_package\Modules\Essentials\Resources\views\policy" mkdir deployment_package\Modules\Essentials\Resources\views\policy
if not exist "deployment_package\Modules\Essentials\Resources\lang\en" mkdir deployment_package\Modules\Essentials\Resources\lang\en

echo Copying files...

REM Copy main files
copy "Modules\Essentials\Entities\PolicyTemplates.php" "deployment_package\Modules\Essentials\Entities\" >nul
copy "Modules\Essentials\Entities\EssentialsPolicy.php" "deployment_package\Modules\Essentials\Entities\" >nul
copy "Modules\Essentials\Http\Controllers\EssentialsPolicyController.php" "deployment_package\Modules\Essentials\Http\Controllers\" >nul
copy "Modules\Essentials\Resources\views\policy\index.blade.php" "deployment_package\Modules\Essentials\Resources\views\policy\" >nul
copy "Modules\Essentials\Resources\lang\en\lang.php" "deployment_package\Modules\Essentials\Resources\lang\en\" >nul

REM Copy update scripts
copy "update_policy_content.php" "deployment_package\" >nul
copy "update_leave_policy_content.php" "deployment_package\" >nul

REM Copy documentation
copy "LIVE_DEPLOYMENT_GUIDE.md" "deployment_package\" >nul
copy "FILES_TO_UPLOAD.txt" "deployment_package\" >nul

echo.
echo ✓ All files copied successfully!
echo.
echo Package created in: deployment_package\
echo.
echo Next steps:
echo 1. Upload all files from deployment_package to live server
echo 2. Run update scripts via SSH
echo 3. Clear caches
echo 4. Test the changes
echo.
echo See LIVE_DEPLOYMENT_GUIDE.md for detailed instructions
echo.
pause
