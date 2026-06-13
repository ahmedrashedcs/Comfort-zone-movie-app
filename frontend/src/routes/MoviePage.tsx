import { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import "../styles/MoviePage.css";
import type { Movie } from "../types/types";
import { getMovieById } from "../services/api";
import AddToWatchlistButton from "../components/AddToWatchlistButton";

export default function MoviePage() {
  const { id } = useParams();
  const movieID = Number(id);

  const [movie, setMovie] = useState<Movie | null>(null);
  const [error, setError] = useState("");

  useEffect(() => {
    const fetchMovie = async () => {
      try {
        const data = await getMovieById(movieID);
        setMovie(data);
      } catch (err) {
        console.error(err);
        setError("Failed to load movie");
      }
    };

    if (!isNaN(movieID)) {
      fetchMovie();
    }
  }, [movieID]);

  if (error) return <p>{error}</p>;
  if (!movie) return <p>Loading...</p>;

  const handleClick = async () => {
    if (movie.homepage) {
      window.location.href = movie.homepage;
    }

    
  };

  return (
    <div className="movie-page">
      <div className="movie-header">
        <img src={movie.poster} alt={movie.title} className="movie-poster" />
        <div className="movie-info">
          <h1>
            {movie.title}{" "}
            <span>({new Date(movie.release_date).getFullYear()})</span>
          </h1>
          <p className="tagline">{movie.tagline} ({movie.original_language})</p> 
          <p>
            <strong>Genres:</strong> {movie.genres?.join(", ") || "N/A"}
          </p>
          <p>
            <strong>User Score:</strong> {movie.vote_average} / 10
          </p>
          <p>
            <strong>Runtime:</strong> {movie.runtime} minutes
          </p>
          <p>
            <strong>Release Date:</strong> {movie.release_date}
          </p>
          <div className="buttons">
            <div className="homepage">
              <button onClick={handleClick}>Movie Page</button>
            </div>
            <AddToWatchlistButton movieID={movieID} />
          </div>
        </div>
      </div>

      <div className="movie-overview">
        <h2>Overview</h2>
        <p>{movie.overview}</p>
      </div>
    </div>
  );
}
