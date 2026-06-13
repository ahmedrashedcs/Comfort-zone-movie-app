import { useEffect, useRef, useState } from "react";
import { getAllMovies } from "../services/api";
import type { MovieSummary } from "../types/types";
import MovieCard from "../components/MovieCard";
import SearchBar from "../components/SearchBar";

export default function MoviesPage() {
  const searchInputRef = useRef<HTMLInputElement>(null);

  const [allMovies, setAllMovies] = useState<MovieSummary[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [sortOption, setSortOption] = useState("Popular");
  const [CurrentPage, setCurrentPage] = useState(1);
  const [query, setQuery] = useState("");

  const handleSearchSubmit = (newQuery: string) => {
    setQuery(newQuery);
    setCurrentPage(1);
  };

  useEffect(() => {
    async function fetchMovies() {
      try {
        setLoading(true);
        const data = await getAllMovies(CurrentPage, sortOption, query);
        setAllMovies(data);
      } catch (err) {
        console.error("Failed to fetch movies:", err);
        setError("Failed to load movies. Please try again later.");
      } finally {
        setLoading(false);
      }
    }

    fetchMovies();
  }, [CurrentPage, sortOption, query]);

  useEffect(() => {
    searchInputRef.current?.focus();
  }, []);

  const handlePrevious = () => {
    if (CurrentPage > 1) {
      setCurrentPage(CurrentPage - 1);
      window.scrollTo({ top: 0, behavior: "smooth" });
    }
  };

  const handleNext = () => {
    setCurrentPage(CurrentPage + 1);
    window.scrollTo({ top: 0, behavior: "smooth" });
  };

  if (loading) return <p className="center-text">Loading movies...</p>;
  if (error) return <p className="center-text error">{error}</p>;

  return (
    <div className="movies-page">
      <div className="movies-header">
        <h2 className="page-title">All Movies</h2>
        <SearchBar ref={searchInputRef} onSubmit={handleSearchSubmit} />
        <select
          className="sort-dropdown"
          value={sortOption}
          onChange={(e) => setSortOption(e.target.value)}
        >
          <option value="Popular">Popular</option>
          <option value="Top Rated">Top Rated</option>
          <option value="New">New</option>
          <option value="Old">Old</option>
        </select>
      </div>

      <div className="movies-grid">
        {allMovies.map((movie) => (
          <MovieCard key={movie.movieID} movie={movie} />
        ))}
      </div>

      <div className="btn">
        <button
          onClick={handlePrevious}
          className="btn1"
          disabled={CurrentPage === 1}
        >
          Previous
        </button>
        <p>
          <strong>{CurrentPage}</strong>
        </p>
        <button onClick={handleNext} className="btn1">
          Next
        </button>
      </div>
    </div>
  );
}