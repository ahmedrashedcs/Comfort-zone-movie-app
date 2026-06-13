import { useEffect, useState } from "react";
import CompletedWatchlistCard from "../components/completedWatchlistCard";
import "../styles/WatchlistMovieCard.css";
import type { CompletedEntry } from "../types/types";
import { getCompleted, removeCompletedEntry } from "../services/api";
import "../styles/WatchlistPage.css";
import { useNavigate } from "react-router-dom";

export default function CompletedPage() {
  const [completedList, setCompletedList] = useState<CompletedEntry[]>([]);
  const [loading, setLoading] = useState(true);
  const apiKey = localStorage.getItem("api_key") || "";
  const navigate = useNavigate();

  useEffect(() => {
    if (!apiKey) navigate("/login");

    getCompleted(apiKey)
      .then(setCompletedList)
      .finally(() => setLoading(false));
  }, [apiKey, navigate]);

  const handleRemove = async (movieID: number) => {
    await removeCompletedEntry(apiKey, movieID);
    setCompletedList((prev) =>
      prev.filter((entry) => entry.movieID !== movieID)
    );
  };

  if (loading) return <p>Loading...</p>;

  return (
    <div className="completed-page">
      <h1>My Completed Watchlist</h1>
      {completedList.length === 0 ? (
        <p>Your completed list is empty.</p>
      ) : (
        <ul className="movie-list">
          {completedList.map((entry) => (
            <CompletedWatchlistCard
              key={entry.compWatchListID}
              entry={entry}
              onRemove={handleRemove}
            />
          ))}
        </ul>
      )}
    </div>
  );
}
