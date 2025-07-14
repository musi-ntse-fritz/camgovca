<?php
// Redirect system for placeholder pages
$page = $_GET['page'] ?? '';

// Define redirects for placeholder pages
$redirects = [
    // PKI Generalities
    'generalites-sur-la-pki' => 'about.php',
    'ra-fr' => 'about.php',
    'tsa-fr' => 'about.php',
    'ocsp-fr' => 'about.php',
    'algorithmes-de-securite' => 'about.php',
    
    // News and Events
    'news-announcements' => 'communiques.php',
    'evenements' => 'communiques.php',
    'mediatheque' => 'communiques.php',
    
    // Products
    'produits' => 'services-list.php',
    
    // Individual certificates
    'individu-fr' => 'demande-de-certificats-fr.php',
    
    // Operations
    'operations' => 'demande-de-certificats-fr.php',
    'emettre-certificat' => 'demande-de-certificats-fr.php',
    'remettre-certificat' => 'demande-de-certificats-fr.php',
    'changer-mot-passe-certificat' => 'demande-de-certificats-fr.php',
    'suspendre-certificat' => 'demande-de-certificats-fr.php',
    'revoquer-certificat' => 'demande-de-certificats-fr.php',
    'copier-certificat' => 'demande-de-certificats-fr.php',
    'renouveler-certificat' => 'demande-de-certificats-fr.php',
    'verifier-identite-certificat' => 'demande-de-certificats-fr.php',
    'verifier-certificat' => 'demande-de-certificats-fr.php',
    'lister-certificats-revoques' => 'demande-de-certificats-fr.php',
    'lister-autorites-certificats-revoques' => 'demande-de-certificats-fr.php',
    
    // Regulations
    'reglementation-politique' => 'about.php',
    'charte-d-abonnement' => 'about.php',
    'lois-et-reglements' => 'about.php',
    'politique-de-certificats' => 'about.php',
    
    // Other pages
    'operator' => 'services-list.php',
];

// Get the target page
$target = $redirects[$page] ?? 'dynamic_home.php';

// Redirect to the target page
header("Location: $target");
exit();
?> 