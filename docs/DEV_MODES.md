# Переключение local Docker ↔ production API

Один переключатель для `front_admin` и `front`, без ручного редактирования `.env`.

## Режимы

| Режим | Команда | API | Admin UI |
|-------|---------|-----|----------|
| **local-api** (по умолчанию) | `.\scripts\set-dev-mode.ps1 -Mode local-api` | Docker `:8888` | `yarn dev` → `:5173` |
| **local-full** | `.\scripts\set-dev-mode.ps1 -Mode local-full` | Docker `:9080` | `:9082` в Docker или `yarn dev` |
| **production** | `.\scripts\set-dev-mode.ps1 -Mode production` | `m.goldtocash.us` | `yarn dev` → прод API |

## Быстрый старт (локальный Docker + админка)

```powershell
# из корня репозитория
.\scripts\set-dev-mode.ps1 -Mode local-api
.\scripts\start-admin-dev.ps1
```

Логин: `admin@local.debug` / `password`

## Полезные команды

```powershell
.\scripts\set-dev-mode.ps1              # показать текущий режим
.\scripts\dev-status.ps1                # режим + health :8888 / :9080 / :5173
.\scripts\start-debug-api.ps1           # только API :8888
.\scripts\start-local-full.ps1        # сайт :9080 + админ :9082
```

## Важно

- После смены режима **перезапустите** `yarn dev` (Vite читает `.env.development.local` при старте).
- Файлы `front_admin/.env.development.local` и `front/.env.development.local` **генерируются** скриптом — шаблоны в `config/dev-modes/`.
- Продакшен на сервере **не использует** эти файлы — только Docker Compose и `docker.env` на droplet.
