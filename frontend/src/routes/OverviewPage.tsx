import { useEffect, useState } from "react";
import { getUserStats } from "../services/api";
import "../styles/OverviewPage.css";

type UserStats = {
  totalTimeWatched: number;
  averageScore: number;
  plannedTime: number;
  lastWatchedMovie: string;
};

export default function OverviewPage() {
  const [stats, setStats] = useState<UserStats | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const userIDString = localStorage.getItem("userID");
  const userID = parseInt(userIDString ?? "0");

  useEffect(() => {
    async function fetchStats() {
      try {
        const data = await getUserStats(userID);
        setStats(data);
      } catch {
        setError("Failed to load user stats.");
      } finally {
        setLoading(false);
      }
    }

    fetchStats();
  }, [userID]);

  if (loading) return <p className="center-text">Loading stats...</p>;
  if (error) return <p className="center-text error">{error}</p>;

  return (
    <div className="overview-details">
      <h2>User Overview</h2>
      <div className="stats-grid">
        <div className="card">
          <h3>Total Time Watched: </h3>
          <p>{stats?.totalTimeWatched} minutes</p>
        </div>
        <div className="card">
          <h3>Your Average Rating: </h3>
          <p>{stats?.averageScore?.toFixed(1)} / 10</p>
        </div>
        <div className="card">
          <h3>Total Watchlist Movies Duration: </h3>
          <p>{stats?.plannedTime} minutes</p>
        </div>
        <div className="card">
          <h3>Last Movie Matched Was At: </h3>
          <p>{stats?.lastWatchedMovie || "N/A"}</p>
        </div>
      </div>
    </div>
  );
}
