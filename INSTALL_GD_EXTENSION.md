# PHP GD Extension Installation Guide

The PDF generation feature requires PHP GD extension to be installed on your server.

## Error Message
```
Error generating PDF: The PHP GD extension is required, but is not installed.
```

## Installation Instructions

### For Windows (XAMPP/WAMP)

1. Open `php.ini` file (usually located in `C:\xampp\php\php.ini`)
2. Find the line: `;extension=gd`
3. Remove the semicolon (`;`) to uncomment it: `extension=gd`
4. Save the file
5. Restart Apache server

### For Linux (Ubuntu/Debian)

```bash
# Install GD extension
sudo apt-get update
sudo apt-get install php-gd

# Restart Apache
sudo service apache2 restart

# Or restart PHP-FPM if using nginx
sudo service php8.1-fpm restart
```

### For Linux (CentOS/RHEL)

```bash
# Install GD extension
sudo yum install php-gd

# Restart Apache
sudo systemctl restart httpd
```

### Verify Installation

After installation, create a file `test_gd.php` with:

```php
<?php
if (extension_loaded('gd')) {
    echo "GD extension is installed!";
    phpinfo();
} else {
    echo "GD extension is NOT installed!";
}
?>
```

Access this file in your browser to verify.

## Alternative Solution (If you cannot install GD)

If you cannot install GD extension, the system will now show the policy as HTML page instead of PDF. Users can then use browser's "Print to PDF" feature to save it as PDF.

## Current Workaround

The code has been updated to:
1. Show a user-friendly error message if PDF generation fails
2. Fall back to HTML view if GD extension is not available
3. Allow users to print the HTML page as PDF using browser

