import type { CompletedEntry } from "../types/types";
import "../styles/WatchlistMovieCard.css";
import { useState } from "react";
import { removeCompletedEntry, updateCompletedRating } from "../services/api";
import { Link, useNavigate } from "react-router-dom";
import AddToWatchlistButton from "./AddToWatchlistButton";


type Props = {
  entry: CompletedEntry;
  onRemove: (movieID: number) => void;
};

export default function CompletedWatchlistCard({ entry, onRemove }: Props) {
  const {
    movieID,
    title,
    overview,
    poster,
    vote_average,
    release_date,
    watch_num,
    rating,
  } = entry;

  const [currentRating, setCurrentRating] = useState(rating);
  const navigate = useNavigate();
  const apiKey = localStorage.getItem("api_key");

  const handleRatingChange = async (
    e: React.ChangeEvent<HTMLSelectElement>
  ) => {
    const newRating = parseFloat(e.target.value);
    setCurrentRating(newRating);

    if (!apiKey) return;

    try {
      await updateCompletedRating(apiKey, movieID, newRating);
      navigate(0);
    } catch (err) {
      console.error("Failed to update rating:", err);
      alert("Could not update rating. Please try again.");
    }
  };

  const handleClick = async () => {
    if (!apiKey) {
      navigate("/login");
      return;
    }

    try {
      await removeCompletedEntry(apiKey, movieID);
      onRemove(movieID);
      navigate(0);
    } catch (err) {
      console.error("Failed to delete entry:", err);
      alert("Failed to delete entry. Please try again.");
    }
  };

  return (
    <li className="watchlist-card">
      <div className="watchlist-poster-div">
        <img src={poster} alt={title} className="poster" />
      </div>
      <div className="w-details">
        <div className="w-header">
          <Link to={`/movie/${movieID}`} className="card-link">
            <h2>{title}</h2>
          </Link>
          <p className="release">{formatDate(release_date)}</p>
        </div>
        <p className="overview">{overview}</p>
        <p><strong>Times Watched: </strong>{watch_num}</p>
        <div className="rating-info">
          <span className="rating">
            <span>Average Rating: </span>
            {vote_average} / 10
          </span>
          <span className="your-rating">
            <span>Your Rating: </span>
            {currentRating} / 10
          </span>
          <label>
            Your Rating:
            <select value={currentRating} onChange={handleRatingChange}>
              {[...Array(11).keys()].map((n) => (
                <option key={n} value={n}>
                  {n}
                </option>
              ))}
            </select>
          </label>
        </div>

        <div className="btn">
          <button className="watched" onClick={handleClick}>
            Delete
          </button>
          <div>
            <AddToWatchlistButton movieID={movieID} />
          </div>
        </div>
      </div>
    </li>
  );
}

function formatDate(dateStr: string) {
  const d = new Date(dateStr);
  return d.toLocaleDateString(undefined, {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
}
