import { useState } from "react";
import { login } from "../services/api";
import "../styles/LoginPage.css";
import { Link, useNavigate } from "react-router-dom";

export default function LoginPage() {
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [errors, setErrors] = useState<{
    username?: boolean;
    password?: boolean;
  }>({});
  const navigate = useNavigate();

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    try {
      const user = await login(username, password);
      localStorage.setItem("user", JSON.stringify(user));
      localStorage.setItem("userID", user.userID.toString());
      localStorage.setItem("api_key", user.api_key);
      navigate("/");
    } catch (err) {
      if (err instanceof Error && err.message.includes("401")) {
        setErrors({ password: true });
      } else {
        setErrors({ username: true });
      }
    }
  };

  return (
    <>
      <header>
        <h1>My Movie API</h1>
      </header>
      <main>
        <section className="container">
          <div id="center-container">
            <h2>Login</h2>
            <form id="login" onSubmit={handleSubmit}>
              <div className="form-item col">
                <label htmlFor="username">Username:</label>
                <div className="box">
                  <input
                    type="text"
                    id="username"
                    name="username"
                    size={25}
                    value={username}
                    onChange={(e) => setUsername(e.target.value)}
                    placeholder="Type your username"
                  />
                  <i className="fa-solid fa-user"></i>
                </div>
                <span className={`error ${!errors.username ? "hidden" : ""}`}>
                  Your username was invalid
                </span>
              </div>

              <div className="form-item col">
                <label htmlFor="password">Password:</label>
                <div className="box">
                  <input
                    type="password"
                    id="password"
                    name="password"
                    size={25}
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    placeholder="Type your password"
                  />
                  <i className="fa-solid fa-lock"></i>
                </div>
                <span className={`error ${!errors.password ? "hidden" : ""}`}>
                  Your password was invalid
                </span>
              </div>

              <button id="submit" name="submit" className="centered">
                Login
              </button>
              <br />
              <Link to="/create-account" className="centered">
                Create a New Account
              </Link>
            </form>
          </div>
        </section>
      </main>
    </>
  );
}
