# EcolePontPeageReact

Refonte complete du projet avec:

- Frontend: React, Vite, Tailwind CSS, Motion, Lucide React, React Router DOM, Recharts, composants style shadcn
- Backend: PHP 8.2+, API REST JSON, SQLite auto-initialisee au premier lancement

## Structure

- `src/`: application frontend
- `backend/public/index.php`: point d'entree de l'API
- `backend/database/schema.sql`: schema SQLite
- `backend/database/seed.php`: jeu de donnees de demonstration

## Lancement

1. Installer les dependances frontend:

```bash
npm install
```

2. Lancer l'API PHP:

```bash
php -S 127.0.0.1:8000 -t backend/public
```

3. Dans un autre terminal, lancer le frontend:

```bash
npm run dev
```

L'API initialise automatiquement `backend/storage/database.sqlite` si la base n'existe pas.

## Comptes de demo

- Admin: `admin@pontpeage.local` / `admin123`
- Operateur: `agent@pontpeage.local` / `operator123`

## Build

```bash
npm run build
```
