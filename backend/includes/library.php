<?php

function connectdb()
{
  static $pdo = null;

  if ($pdo instanceof PDO) {
    return $pdo;
  }

  $databasePath = dirname(__DIR__) . '/database/app.sqlite';
  $databaseDir = dirname($databasePath);

  if (!is_dir($databaseDir)) {
    mkdir($databaseDir, 0777, true);
  }

  try {
    $pdo = new PDO('sqlite:' . $databasePath, null, null, [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    $pdo->exec('PRAGMA foreign_keys = ON');
  } catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
  }

  return $pdo;
}
