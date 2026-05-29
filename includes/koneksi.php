<?php
// includes/koneksi.php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/FirestoreREST.php';

use Kreait\Firebase\Factory;

// Initialize Firebase Auth
$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/../config/firebase_credentials.json');
$auth = $factory->createAuth();

// Initialize custom Firestore REST client
$database = new FirestoreREST(__DIR__ . '/../config/firebase_credentials.json');
?>