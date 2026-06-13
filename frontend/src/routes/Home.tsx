import { useEffect, useState } from "react";
import type {
  CompletedEntry,
  MovieSummary,
  WatchlistEntry,
} from "../types/types";
import MovieCard from "../components/MovieCard";
import { getAllMovies, getCompleted, getWatchlist } from "../services/api";
import "../styles/Home.css";

const GENRES = ["Family", "Action", "Comedy", "Romance", "Horror"];
const SORT_OPTIONS = ["Popular", "Top Rated", "New", "Old"];

export function Home() {
  const [movies, setMovies] = useState<MovieSummary[]>([]);
  const [genre, setGenre] = useState("Family");
  const [sortBy, setSortBy] = useState("Popular");
  const [error, setError] = useState("");

  const [watchlistMovies, setWatchlistMovies] = useState<WatchlistEntry[]>([]);
  const [completedMovies, setCompletedMovies] = useState<CompletedEntry[]>([]);

  const apiKey = localStorage.getItem("api_key");

  useEffect(() => {
    async function fetchUserLists() {
      if (!apiKey) {
        try {
          const all = await getAllMovies(1, sortBy, undefined, genre);

          setMovies(all.slice(0, 20));
          setError("");
        } catch (err) {
          console.error("Failed to fetch movies:", err);
          setError("Failed to load movies. Make sure the backend API is running and VITE_API_BASE_URL is set correctly.");
        }
      } else {
        try {
          const [all, watchlist, completed] = await Promise.all([
            getAllMovies(1, sortBy, undefined, genre),
            getWatchlist(apiKey),
            getCompleted(apiKey),
          ]);

          setMovies(all.slice(0, 20));

          setWatchlistMovies(watchlist.slice(0, 10));

          setCompletedMovies(completed.slice(0, 10));
          setError("");
        } catch (err) {
          console.error("Failed to fetch movies:", err);
          setError("Failed to load movies. Make sure the backend API is running and VITE_API_BASE_URL is set correctly.");
        }
      }
    }

    fetchUserLists();
  }, [apiKey, genre, sortBy]);

  return (
    <div className="home-page">
      <h1>Movie Explorer</h1>

      <div className="sort-filters">
        <div className="filters">
          {SORT_OPTIONS.map((s) => (
            <label
              key={s}
              className={`radio-label2 ${sortBy === s ? "selected" : ""}`}
            >
              <input
                type="radio"
                name="sort"
                value={s}
                checked={sortBy === s}
                onChange={() => setSortBy(s)}
              />
              {s}
            </label>
          ))}
        </div>
      </div>

      <div className="genre-filters">
        <h3>Choose Genre</h3>
        <div className="filters">
          {GENRES.map((g) => (
            <label
              key={g}
              className={`radio-label1 ${genre === g ? "selected" : ""}`}
            >
              <input
                type="radio"
                name="genre"
                value={g}
                checked={genre === g}
                onChange={() => setGenre(g)}
              />
              {g}
            </label>
          ))}
        </div>
      </div>

      <h2>
        {sortBy} {genre} Movies
      </h2>
      {error && <p className="center-text error">{error}</p>}
      <div className="movie-grid">
        {movies.map((movie) => (
          <MovieCard key={movie.movieID} movie={movie} />
        ))}
      </div>

      {apiKey && (
        <>
          <h2>Your Watchlist</h2>
          <div className="movie-grid">
            {watchlistMovies.map((movie) => (
              <MovieCard key={movie.movieID} movie={movie} />
            ))}
          </div>

          <h2>Your Completed Movies</h2>
          <div className="movie-grid">
            {completedMovies.map((movie) => (
              <MovieCard key={movie.movieID} movie={movie} />
            ))}
          </div>
        </>
      )}
    </div>
  );
}
