# Movie Tracker

A full-stack movie tracking application that allows users to discover movies, manage personal watchlists, track completed movies, and maintain their own movie ratings and viewing history.

## Features

* Secure user authentication
* Browse and search movie collections
* Filter and sort movies
* View detailed movie information
* Add and remove movies from a personal watchlist
* Mark movies as completed
* Track viewing history and watch counts
* Rate completed movies
* View personalized movie statistics
* Manage watchlist priorities

## Technology Stack

### Frontend

* React
* TypeScript
* Vite
* React Router
* Axios

### Backend

* PHP
* REST API
* MySQL

## Installation

### Clone the Repository

```bash
git clone <repository-url>
cd movie-tracker
```

### Install Dependencies

```bash
npm install
```

### Configure Environment Variables

Create a `.env.local` file in the project root:

```env
VITE_API_BASE_URL="https://your-api-url.com/api"
```

### Run the Development Server

```bash
npm run dev
```

The application will be available at:

```text
http://localhost:5173
```

### Build for Production

```bash
npm run build
```

## Application Functionality

### User Accounts

Users can create accounts, log in securely, and maintain personalized movie collections.

### Movie Discovery

Browse, search, and filter movies by various criteria, including popularity, release date, and ratings.

### Watchlist Management

Save movies for future viewing and organize them using custom priority levels.

### Completed Movies

Track watched movies, record personal ratings, and monitor rewatch counts.

### Personal Statistics

View insights such as total watch time, average ratings, and other viewing metrics.

## API Integration

The frontend communicates with a RESTful backend API that provides:

* Movie data retrieval
* Authentication services
* Watchlist management
* Completed movie tracking
* User statistics

## Future Enhancements

* Personalized movie recommendations
* Social features and friend activity
* User reviews and comments
* Advanced analytics dashboard
* Dark mode support
* Responsive mobile experience
* Third-party movie database integration

## Author

Ahmed Rashed

Software Engineer | Full-Stack Developer

LinkedIn: linkedin.com/in/ahmed-hosam-rashed
GitHub: https://github.com/ahmedrashedcs
