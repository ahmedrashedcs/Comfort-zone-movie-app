import { Outlet } from 'react-router-dom';
import Layout from './components/Layout';
import "./styles/MoviesPage.css"


export function App() {
  return (
    <Layout>
      <Outlet />
    </Layout>
  );
}

export default App
