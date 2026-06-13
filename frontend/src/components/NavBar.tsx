import { NavLink } from "react-router-dom";
import "../styles/NavBar.css";
import { useEffect, useState } from "react";

export default function NavBar() {
  const [isLoggedIn, setIsLoggedIn] = useState(
    !!localStorage.getItem("api_key")
  );

  useEffect(() => {
    const handleStorageChange = () => {
      setIsLoggedIn(!!localStorage.getItem("api_key"));
    };

    window.addEventListener("storage", handleStorageChange);

    window.addEventListener("focus", handleStorageChange);

    return () => {
      window.removeEventListener("storage", handleStorageChange);
      window.removeEventListener("focus", handleStorageChange);
    };
  }, []);

  return (
    <section className="header">
    <nav className="navbar">
      <div className="logo">
        <NavLink to="/">
          Comfort <span>Zone</span>
        </NavLink>
      </div>
      <ul className="navigation">
        <li>
          <NavLink
            to="/movies"
            className={({ isActive }) => (isActive ? "active" : "")}
          >
            Movies
          </NavLink>
        </li>

        {isLoggedIn && (
          <>
            <li>
              <NavLink
                to="/watchlist"
                className={({ isActive }) => (isActive ? "active" : "")}
              >
                Watchlist
              </NavLink>
            </li>
            <li>
              <NavLink
                to="/completed"
                className={({ isActive }) => (isActive ? "active" : "")}
              >
                Completed
              </NavLink>
            </li>
            <li>
              <NavLink
                to="/overview"
                className={({ isActive }) => (isActive ? "active" : "")}
                >
                  Overview
              </NavLink>
            </li>
          </>
        )}

        <li>
          <NavLink
            to={isLoggedIn ? "/logout" : "/login"}
            className={({ isActive }) => (isActive ? "active" : "")}
          >
            {isLoggedIn ? "Logout" : "Login"}
          </NavLink>
        </li>
      </ul>
    </nav>
    </section>
  );
}
