import { createBrowserRouter, type RouteObject } from "react-router-dom";
import App from "./App";
import LoginPage from "./routes/LoginPage";
import ErrorPage from "./routes/ErrorPage";
import MoviesPage from "./routes/MoviesPage";
import WatchlistPage from "./routes/WatchlistPage";
import CompletedPage from "./routes/CompletedPage";
import Logout from "./components/Logout";
import MoviePage from "./routes/MoviePage";
import { Home } from "./routes/Home";
import OverviewPage from "./routes/OverviewPage";
import CreateAccountPage from "./routes/CreateAccountPage";

const routes: RouteObject[] = [
  {
    path: "/",
    element: <App />,
    errorElement: <ErrorPage />,
    children: [
      { index: true, element: <Home /> },
      { path: "movies", element: <MoviesPage /> },
      { path: "movie/:id", element: <MoviePage /> },
      { path: "watchlist", element: <WatchlistPage /> },
      { path: "completed", element: <CompletedPage /> },
      { path: "overview", element: <OverviewPage />},
      { path: "login", element: <LoginPage /> },
      { path: "create-account", element: <CreateAccountPage /> },
      { path: "logout", element: <Logout /> },
    ],
  },
];



const router = createBrowserRouter(routes, {
  basename: import.meta.env.BASE_URL,
});

export default router;
