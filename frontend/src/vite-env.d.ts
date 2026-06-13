interface ImportMetaEnv {
  readonly VITE_API_BASE_URL: string;
  readonly VITE_CREATE_ACCOUNT_URL?: string;
  readonly BASE_URL: string;
}

interface ImportMeta {
  readonly env: ImportMetaEnv;
}

declare module "*.css";
