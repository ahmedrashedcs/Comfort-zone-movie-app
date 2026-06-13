import type {
  CompletedEntry,
  MovieSummary,
  WatchlistEntry,
} from "../types/types";
import "../styles/MovieCard.css";
import { Link } from "react-router-dom";
import AddToWatchlistButton from "./AddToWatchlistButton";

type Props = {
  movie: MovieSummary | WatchlistEntry | CompletedEntry;
};

export default function MovieCard({ movie }: Props) {
  let genres: string[] = [];

  const rawGenres = movie.genres as unknown;

  if (Array.isArray(rawGenres)) {
    genres = rawGenres as string[];
  } else if (typeof rawGenres === "string") {
    genres = rawGenres.split(",").map((g) => g.trim());
  }

  return (
    <div className="movie-card">
      <div className="glow-effect"></div>

      <div className="poster-div">
        <img
          className="movie-card-poster"
          src={movie.poster}
          alt={movie.title}
        />
      </div>

      <div className="movie-details">
        <div>
          <Link to={`/movie/${movie.movieID}`} className="movie-card-link">
            <h3 className="movie-title">{movie.title}</h3>
          </Link>
          <p className="movie-release">
            <strong>Release: </strong>
            {new Date(movie.release_date).getFullYear()}
          </p>
        </div>

        <div className="badges">
          {genres.map((genre) => (
            <span key={genre} className="badge">
              {genre}
            </span>
          ))}
        </div>

        <div className="movie-info">
          <p className="movie-rating">{movie.vote_average} / 10</p>

          <div className="rating-container">
            <div className="rating-bar">
              <div
                className="rating-fill"
                style={{ width: `${movie.vote_average * 10}%` }}
              ></div>
            </div>
            <div className="rating-value">{movie.vote_average * 10}%</div>
          </div>
        </div>
      </div>

      <div className="action-bar">
        <AddToWatchlistButton movieID={movie.movieID} />
      </div>
    </div>
  );
}
