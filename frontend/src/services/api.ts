import axios from "axios";
import type {
  User,
  MovieSummary,
  WatchlistEntry,
  CompletedEntry,
  Movie,
} from "../types/types";

const rawApiBaseUrl = import.meta.env.VITE_API_BASE_URL;
const apiBaseUrl = rawApiBaseUrl ? rawApiBaseUrl.replace(/\/+$/, "") : "";

const api = axios.create({
  baseURL: apiBaseUrl ? `${apiBaseUrl}/` : "/",
  headers: {
    "Content-Type": "application/json",
  },
});

function ensureArray<T>(value: unknown, endpoint: string): T[] {
  if (Array.isArray(value)) {
    return value as T[];
  }

  throw new Error(`Expected an array response from ${endpoint}`);
}

export async function login(username: string, password: string): Promise<User> {
  const res = await api.post<User>("users/session", { username, password });
  return res.data;
}

export async function getAllMovies(page: number = 1, sort?: string, search?: string ,genre?: string): Promise<MovieSummary[]> {
  let url = `movies?page=${page}`;

  if (genre) {
    url += `&genre=${genre}`;
  }

  if (sort) {
    url += `&sort=${sort}`;
  }
  
  if (search) {
    url += `&search=${search}`;
  }

  const res = await api.get<MovieSummary[]>(url);
  return ensureArray<MovieSummary>(res.data, url);
}

export async function getMovieById(id: number): Promise<Movie> {
  const res = await api.get<Movie>(`movies/${id}`);
  return res.data;
}

export async function getWatchlist(apiKey: string): Promise<WatchlistEntry[]> {
  const res = await api.get<WatchlistEntry[]>("towatchlist/entries", {
    headers: { "X-API-KEY": apiKey },
  });
  return ensureArray<WatchlistEntry>(res.data, "towatchlist/entries");
}

export async function addToWatchlist(
  apiKey: string,
  movieID: number,
  priority = 6,
  notes = ""
): Promise<void> {
  await api.post(
    "towatchlist/entries",
    { movieID, priority, notes },
    { headers: { "X-API-KEY": apiKey } }
  );
}

export async function removeFromWatchlist(
  apiKey: string,
  movieID: number
): Promise<void> {
  await api.delete(`towatchlist/entries/${movieID}`, {
    headers: { "X-API-KEY": apiKey },
  });
}

export async function updateWatchlistEntry(
  apiKey: string,
  movieID: number,
  priority: number,
  notes: string
): Promise<void> {
  await api.patch(
    `towatchlist/entries/${movieID}`,
    { priority, notes },
    { headers: { "X-API-KEY": apiKey } }
  );
}

export async function updateWatchlistPriority(
  apiKey: string,
  movieID: number,
  priority: number
): Promise<void> {
  await api.patch(
    `towatchlist/entries/${movieID}/priority`,
    { priority },
    { headers: { "X-API-KEY": apiKey } }
  );
}

export async function getCompleted(apiKey: string): Promise<CompletedEntry[]> {
  const res = await api.get<CompletedEntry[]>("completedwatchlist/entries", {
    headers: { "X-API-KEY": apiKey },
  });
  return ensureArray<CompletedEntry>(res.data, "completedwatchlist/entries");
}

export async function getTimesWatched(
  apiKey: string,
  movieID: number
): Promise<number> {
  const res = await api.get<{ movieID: number; watch_num: number }[]>(
    `completedwatchlist/entries/${movieID}/times-watched`,
    {
      headers: { "X-API-KEY": apiKey },
    }
  );

  const data = ensureArray<{ movieID: number; watch_num: number }>(
    res.data,
    `completedwatchlist/entries/${movieID}/times-watched`
  );

  if (data.length === 0) return 0;

  return data[0].watch_num ?? 0;
}

export async function addToCompleted(
  apiKey: string,
  movieID: number,
  notes: string
): Promise<void> {
  await api.post(
    "completedwatchlist/entries",
    { movieID, notes },
    { headers: { "X-API-KEY": apiKey } }
  );
}

export async function updateCompletedEntry(
  apiKey: string,
  compWatchListID: number,
  updates: Partial<
    Pick<CompletedEntry, "rating" | "notes" | "last_watch" | "watch_num">
  >
): Promise<void> {
  await api.patch(`completedwatchlist/entries/${compWatchListID}`, updates, {
    headers: { "X-API-KEY": apiKey },
  });
}

export async function updateCompletedRating(
  apiKey: string,
  movieID: number,
  rating: number
): Promise<void> {
  await api.patch(
    `completedwatchlist/entries/${movieID}/rating`,
    { rating },
    { headers: { "X-API-KEY": apiKey } }
  );
}

export async function incrementTimesWatched(
  apiKey: string,
  movieID: number
): Promise<void> {
  await api.patch(
    `completedwatchlist/entries/${movieID}/times-watched`,
    {},
    {
      headers: { "X-API-KEY": apiKey },
    }
  );
}

export async function removeCompletedEntry(
  apiKey: string,
  movieID: number
): Promise<void> {
  await api.delete(`completedwatchlist/entries/${movieID}`, {
    headers: { "X-API-KEY": apiKey },
  });
}

export async function getUserStats(userID: number) {
  const res = await api.get(`/users/${userID}/stats`);
  return res.data;
}

export function getCreateAccountUrl(): string {
  if (import.meta.env.VITE_CREATE_ACCOUNT_URL) {
    return import.meta.env.VITE_CREATE_ACCOUNT_URL;
  }

  return apiBaseUrl.replace(/\/api$/, "/create-account.php");
}
