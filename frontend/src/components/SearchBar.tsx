import { forwardRef, useState } from "react";
import "../App.css";
import "../styles/SearchBar.css";

type Props = {
  onSubmit: (query: string) => void;
};

const SearchBar = forwardRef<HTMLInputElement, Props>(({ onSubmit }, ref) => {
  const [localQuery, setLocalQuery] = useState("");

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    onSubmit(localQuery.trim());
  };

  return (
    <form className="search-bar" onSubmit={handleSubmit}>
      <div>
        <input
          id="search"
          type="text"
          ref={ref}
          value={localQuery}
          placeholder="Search movies..."
          onChange={(e) => setLocalQuery(e.target.value)}
        />
        <i className="fa-solid fa-magnifying-glass"></i>
      </div>
      <button type="submit">Search</button>
    </form>
  );
});

export default SearchBar;
