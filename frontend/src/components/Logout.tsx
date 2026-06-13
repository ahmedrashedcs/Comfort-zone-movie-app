import { useEffect } from "react";
import { useNavigate } from "react-router-dom";

export default function Logout() {
  const navigate = useNavigate();

  useEffect(() => {
    localStorage.removeItem("api_key");
    localStorage.removeItem("user");
    localStorage.removeItem("userID");

    window.dispatchEvent(new Event("storage"));

    navigate("/");
  }, [navigate]);

  return null;
}
