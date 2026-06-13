import { useEffect } from "react";
import { getCreateAccountUrl } from "../services/api";

export default function CreateAccountPage() {
  const createAccountUrl = getCreateAccountUrl();

  useEffect(() => {
    window.location.assign(createAccountUrl);
  }, [createAccountUrl]);

  return (
    <div style={{ padding: "2rem", textAlign: "center" }}>
      <p>Redirecting to account creation...</p>
      <p>
        If nothing happens, <a href={createAccountUrl}>open the signup page</a>.
      </p>
    </div>
  );
}
