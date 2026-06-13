import type { ReactNode } from "react";
import "../styles/Layout.css"
import NavBar from "./NavBar";

export default function Layout({ children }: { children: ReactNode }) {
  return (
    <div className="layout">
      <header className="h-header">
          <NavBar />
      </header>

      <main>{children}</main>
    </div>
  );
}
