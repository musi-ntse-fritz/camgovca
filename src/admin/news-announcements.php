<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

$pdo = getDBConnection();
if (!$pdo) {
    die('Database connection failed');
}

// Get news and announcements (we'll use the pages table for this)
$stmt = $pdo->query("SELECT page_id, page_title, page_slug, status, created_at FROM pages WHERE page_slug LIKE '%news%' OR page_slug LIKE '%announcement%' ORDER BY created_at DESC");
$news = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CamGovCA Admin - News & Announcements</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f6f9;
        }
        .header {
            background: #667eea;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .nav-links {
            display: flex;
            gap: 20px;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            background: rgba(255,255,255,0.2);
            border-radius: 5px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .btn {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        .news-grid {
            display: grid;
            gap: 20px;
        }
        .news-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .news-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        .news-title {
            font-weight: bold;
            color: #333;
            font-size: 18px;
            margin: 0;
            flex: 1;
        }
        .news-meta {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        .news-status {
            background: #d4edda;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            color: #155724;
        }
        .news-status.draft {
            background: #fff3cd;
            color: #856404;
        }
        .news-status.archived {
            background: #f8d7da;
            color: #721c24;
        }
        .news-date {
            color: #666;
            font-size: 12px;
        }
        .news-actions {
            display: flex;
            gap: 10px;
        }
        .btn-small {
            padding: 5px 10px;
            font-size: 12px;
        }
        .btn-edit {
            background: #17a2b8;
        }
        .btn-delete {
            background: #dc3545;
        }
        .btn-publish {
            background: #28a745;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>News & Announcements</h1>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="page-header">
            <h2>Manage News & Announcements</h2>
            <a href="#" class="btn">Add New Article</a>
        </div>
        
        <div class="news-grid">
            <?php if (empty($news)): ?>
            <div class="news-card">
                <div class="empty-state">
                    <h3>No news articles found</h3>
                    <p>Create your first news article or announcement to get started.</p>
                </div>
            </div>
            <?php else: ?>
                <?php foreach ($news as $article): ?>
                <div class="news-card">
                    <div class="news-header">
                        <h3 class="news-title"><?php echo htmlspecialchars($article['page_title']); ?></h3>
                        <div class="news-actions">
                            <a href="#" class="btn btn-small btn-edit">Edit</a>
                            <a href="#" class="btn btn-small btn-delete">Delete</a>
                            <?php if ($article['status'] == 'draft'): ?>
                                <a href="#" class="btn btn-small btn-publish">Publish</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="news-meta">
                        <span class="news-status <?php echo $article['status']; ?>">
                            <?php echo ucfirst(htmlspecialchars($article['status'])); ?>
                        </span>
                        <span class="news-date">
                            Created: <?php echo date('M j, Y', strtotime($article['created_at'])); ?>
                        </span>
                    </div>
                    
                    <div style="color: #666; margin-bottom: 10px;">
                        Slug: <?php echo htmlspecialchars($article['page_slug']); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 