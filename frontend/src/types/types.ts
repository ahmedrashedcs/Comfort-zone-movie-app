export interface User {
  userID: number;
  api_key: string;
}

export interface MovieSummary {
  movieID: number;
  title: string;
  poster: string;
  vote_average: number;
  vote_count: number;
  release_date: string;
  genres: string[] | string;
}

export interface Movie extends MovieSummary {
  tagline: string | null;
  overview: string;
  original_language: string;
  runtime: number;
  budget: number;
  revenue: number;
  homepage: string | null;
  genres: string[];
}

export interface WatchlistEntry extends MovieSummary {
  userID: number;
  priority: number;
  notes: string;
  overview: string;
}

export interface CompletedEntry extends MovieSummary {
  compWatchListID: number;
  userID: number;
  rating: number;
  notes: string;
  overview: string;
  watch_num: number;
  last_watch: string;
}
