PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS users (
  userID INTEGER PRIMARY KEY AUTOINCREMENT,
  username TEXT NOT NULL UNIQUE,
  email TEXT NOT NULL UNIQUE,
  pwd TEXT NOT NULL,
  api_key TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS movies (
  movieID INTEGER PRIMARY KEY,
  title TEXT NOT NULL,
  tagline TEXT,
  overview TEXT,
  original_language TEXT,
  poster TEXT,
  runtime INTEGER DEFAULT 0,
  vote_average REAL DEFAULT 0,
  vote_count INTEGER DEFAULT 0,
  budget INTEGER DEFAULT 0,
  revenue INTEGER DEFAULT 0,
  homepage TEXT,
  release_date TEXT
);

CREATE TABLE IF NOT EXISTS genres (
  genreID INTEGER PRIMARY KEY,
  name TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS production_companies (
  companyID INTEGER PRIMARY KEY,
  name TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS movie_genres (
  movieID INTEGER NOT NULL,
  genreID INTEGER NOT NULL,
  PRIMARY KEY (movieID, genreID),
  FOREIGN KEY (movieID) REFERENCES movies(movieID) ON DELETE CASCADE,
  FOREIGN KEY (genreID) REFERENCES genres(genreID) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS movie_companies (
  movieID INTEGER NOT NULL,
  companyID INTEGER NOT NULL,
  PRIMARY KEY (movieID, companyID),
  FOREIGN KEY (movieID) REFERENCES movies(movieID) ON DELETE CASCADE,
  FOREIGN KEY (companyID) REFERENCES production_companies(companyID) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS toWatchList (
  toWatchListID INTEGER PRIMARY KEY AUTOINCREMENT,
  movieID INTEGER NOT NULL,
  userID INTEGER NOT NULL,
  priority INTEGER NOT NULL DEFAULT 1 CHECK (priority BETWEEN 1 AND 10),
  notes TEXT NOT NULL DEFAULT '',
  UNIQUE (movieID, userID),
  FOREIGN KEY (movieID) REFERENCES movies(movieID) ON DELETE CASCADE,
  FOREIGN KEY (userID) REFERENCES users(userID) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS completedWatchList (
  compWatchListID INTEGER PRIMARY KEY AUTOINCREMENT,
  movieID INTEGER NOT NULL,
  userID INTEGER NOT NULL,
  rating INTEGER NOT NULL DEFAULT 0 CHECK (rating BETWEEN 0 AND 10),
  notes TEXT NOT NULL DEFAULT '',
  initial_watch TEXT NOT NULL,
  last_watch TEXT NOT NULL,
  watch_num INTEGER NOT NULL DEFAULT 1 CHECK (watch_num >= 1),
  UNIQUE (movieID, userID),
  FOREIGN KEY (movieID) REFERENCES movies(movieID) ON DELETE CASCADE,
  FOREIGN KEY (userID) REFERENCES users(userID) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_movies_title ON movies(title);
CREATE INDEX IF NOT EXISTS idx_users_api_key ON users(api_key);
CREATE INDEX IF NOT EXISTS idx_movie_genres_genre ON movie_genres(genreID);
CREATE INDEX IF NOT EXISTS idx_movie_companies_company ON movie_companies(companyID);
CREATE INDEX IF NOT EXISTS idx_towatch_user ON toWatchList(userID);
CREATE INDEX IF NOT EXISTS idx_completed_user ON completedWatchList(userID);
