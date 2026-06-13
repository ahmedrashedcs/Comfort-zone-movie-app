import type { WatchlistEntry } from "../types/types";
import "../styles/WatchlistMovieCard.css";
import { useState } from "react";
import {
  addToCompleted,
  getTimesWatched,
  incrementTimesWatched,
  removeFromWatchlist,
  updateWatchlistPriority,
} from "../services/api";
import { Link, useNavigate } from "react-router-dom";
import AddToWatchlistButton from "./AddToWatchlistButton";

type Props = {
  entry: WatchlistEntry;
  onRemove: (movieID: number) => void;
};

export default function WatchlistMovieCard({ entry }: Props) {
  const {
    movieID,
    title,
    overview,
    poster,
    vote_average,
    release_date,
    priority,
  } = entry;
  const [currentPriority, setCurrentPriority] = useState(priority);
  const navigate = useNavigate();
  const apiKey = localStorage.getItem("api_key");

  const handlePriorityChange = async (
    e: React.ChangeEvent<HTMLSelectElement>
  ) => {
    const newPriority = parseInt(e.target.value);
    setCurrentPriority(newPriority);

    const apiKey = localStorage.getItem("api_key");
    if (!apiKey) return;

    try {
      await updateWatchlistPriority(apiKey, movieID, newPriority);
      navigate(0);
    } catch (err) {
      console.error("Failed to update priority:", err);
      alert("Could not update priority. Please try again.");
    }
  };

  const handleClick = async () => {
    if (!apiKey) return;
    
    await removeFromWatchlist(apiKey, movieID);
    
    try {
      
      const timesWatched = await getTimesWatched(apiKey, movieID);
      
      if (timesWatched >= 1) {
        await incrementTimesWatched(apiKey, movieID);        
      } else {
        await addToCompleted(apiKey, movieID, "");        
      }
    } catch (err) {
      console.error("Error updating completed list:", err);
    }    
    
    navigate(0);    
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
        <span className="rating">
          <span>Rating: </span>
          {Math.round(vote_average) * 10}%
        </span>

        <label>
          Priority:
          <select value={currentPriority} onChange={handlePriorityChange}>
            {[1, 2, 3, 4, 5, 6].map((p) => (
              <option key={p} value={p}>
                {p} {p === 1 ? "(Highest)" : p === 6 ? "(Lowest)" : ""}
              </option>
            ))}
          </select>
        </label>
        <div className="btn">
          <button className="watched" onClick={handleClick}>
            Watched
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
