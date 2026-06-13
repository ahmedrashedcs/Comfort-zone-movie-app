import { useState, useEffect } from "react";
import {
  addToWatchlist,
  removeFromWatchlist,
  getWatchlist,
} from "../services/api";
import "../styles/AddToWatchlist.css";
import { useNavigate } from "react-router-dom";

type Props = {
  movieID: number;
};

export default function AddToWatchlistButton({ movieID }: Props) {
  const [inWatchlist, setInWatchlist] = useState(false);
  const apiKey = localStorage.getItem("api_key");
  const navigate = useNavigate();

  useEffect(() => {
    if (!apiKey) return;
    getWatchlist(apiKey).then((entries) => {
      const found = entries.some((entry) => entry.movieID === Number(movieID));
      setInWatchlist(found);
    });
  }, [apiKey, movieID]);

  const handleClick = async () => {
    if (!apiKey) {
      navigate("/login");
      return;
    }

    if (inWatchlist) {
      await removeFromWatchlist(apiKey, movieID);
      setInWatchlist(false);
    } else {
      await addToWatchlist(apiKey, movieID);
      setInWatchlist(true);
    }
  };

  return (
    <button onClick={handleClick} className="add-watch-list-button">
      {inWatchlist ? "- Remove from Watchlist" : "+ Add to Watchlist"}
    </button>
  );
}
