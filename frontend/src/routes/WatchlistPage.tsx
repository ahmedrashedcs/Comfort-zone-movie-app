import { useEffect, useState } from "react";
import { getWatchlist, removeFromWatchlist } from "../services/api";
import type { WatchlistEntry } from "../types/types";
import WatchlistMovieCard from "../components/WatchlistMovieCard";
import "../styles/WatchlistPage.css";
import { useNavigate } from "react-router-dom";

export default function WatchlistPage() {
  const [watchlist, setWatchlist] = useState<WatchlistEntry[]>([]);
  const [loading, setLoading] = useState(true);
  const apiKey = localStorage.getItem("api_key") || "";
  const navigate = useNavigate();

  useEffect(() => {
    if (!apiKey) {
      navigate("/login");
      return;
    }
    
    getWatchlist(apiKey)
      .then((data) => {
        const sorted = data.sort((a, b) => a.priority - b.priority);
        setWatchlist(sorted);
      })
      .finally(() => setLoading(false));
  }, [apiKey, navigate]);

  const handleRemove = async (movieID: number) => {
    await removeFromWatchlist(apiKey, movieID);
    setWatchlist((prev) => prev.filter((item) => item.movieID !== movieID));
  };

  if (loading) return <p>Loading...</p>;
  if (watchlist.length === 0) return <p>Your watchlist is empty.</p>;

  const groupedByPriority: { [priority: number]: WatchlistEntry[] } = {};
  for (const entry of watchlist) {
    if (!groupedByPriority[entry.priority]) {
      groupedByPriority[entry.priority] = [];
    }
    groupedByPriority[entry.priority].push(entry);
  }

  return (
    <div className="watchlist-page">
      <h1>My Watchlist</h1>
      {Object.keys(groupedByPriority)
        .sort((a, b) => Number(a) - Number(b))
        .map((priority) => (
          <div key={priority} className="priority-group">
            <h2 className="priority-label">Priority {priority}</h2>
            <ul className="movie-list">
              {groupedByPriority[Number(priority)].map((entry) => (
                <WatchlistMovieCard
                  key={entry.movieID}
                  entry={entry}
                  onRemove={handleRemove}
                />
              ))}
            </ul>
          </div>
        ))}
    </div>
  );
}
