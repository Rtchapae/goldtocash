# Деплой на production и работа с Cursor Mobile

Сервер: `root@159.89.186.46`, путь: `/var/www/goldtocash.us`

## Быстрый старт (после правок в git)

```powershell
# из корня репозитория, на машине с SSH-ключом к серверу
git pull
.\scripts\deploy-from-git.ps1 -Mode last-commit -BackupAndRollback
```

Если меняли **только front** — Docker пересоберёт `front` (~2 мин).  
Если меняли **back/** — контейнер `backend` перезапустится.  
Для **front_admin/** добавьте `-BuildAdmin`.

## Режимы `deploy-from-git.ps1`

| Режим | Когда использовать |
|-------|-------------------|
| `uncommitted` | Есть локальные правки, ещё не закоммичены (по умолчанию) |
| `staged` | Только `git add`'нутые файлы |
| `last-commit` | **После commit** — залить последний коммит на сервер |
| `since-main` | Всё, что изменилось с `origin/main` |
| `manifest` | Список из `deploy/manifests/*.txt` |
| `paths` | Явные пути: `-Paths "front/src/App.vue"` |

### Примеры

```powershell
# После коммита (рекомендуется)
.\scripts\deploy-from-git.ps1 -Mode last-commit -BackupAndRollback

# Незакоммиченные правки сразу на сервер
.\scripts\deploy-from-git.ps1

# Готовый набор файлов (PageSpeed, Customer.io и т.д.)
.\scripts\deploy-from-git.ps1 -Mode manifest -Manifest mobile-pagespeed.txt
.\scripts\deploy-from-git.ps1 -Mode manifest -Manifest customerio-kit-email.txt

# Админка
.\scripts\deploy-from-git.ps1 -Mode last-commit -BuildAdmin

# Миграции БД
.\scripts\deploy-from-git.ps1 -Mode last-commit -RunMigrations
```

## Workflow: Cursor на телефоне

1. **Редактируете код** в Cursor Mobile (cloud agent или SSH к репо).
2. **Коммит + push** в git:
   ```bash
   git add front/src/...
   git commit -m "fix: ..."
   git push
   ```
3. **Деплой** — на ПК с SSH (или там, где есть ключ к `159.89.186.46`):
   ```powershell
   git pull
   .\scripts\deploy-from-git.ps1 -Mode last-commit -BackupAndRollback
   ```
4. Попросите агента: *«задеплой последний коммит на прод»* — он должен выполнить команду выше.

> Cursor Mobile сам по себе **не имеет SSH к вашему droplet**, если ключ настроен только на домашнем ПК.  
> Варианты: деплой с ПК после `git pull`, или настроить SSH-ключ на машине, где открыт Cursor.

## Что заливается на сервер

Только пути с префиксами: `front/`, `back/`, `docker/`, `front_admin/`, `config/`, `scripts/`, `deploy/`.

**Не деплоятся:** `node_modules`, `.env`, `tmp/`, `dist/`, логи.

## Манифесты

Списки файлов для повторного деплоя: `deploy/manifests/`

- `mobile-pagespeed.txt` — оптимизация мобильной главной
- `customerio-kit-email.txt` — Customer.io + SMS kit flow

## Низкоуровневый скрипт

`deploy-staging.ps1` — тот же механизм (tgz → scp → extract → docker), но файл-лист задаётся параметрами:

```powershell
.\deploy-staging.ps1 -DeployFiles "front/src/App.vue"
.\deploy-staging.ps1 -FilesFrom deploy/manifests/mobile-pagespeed.txt
.\deploy-staging.ps1 -GitSince "HEAD~1..HEAD"
```

## Откат

С флагом `-BackupAndRollback` перед деплоем создаётся бэкап текущих файлов на сервере; при ошибке — автоматический откат.

## Локальная разработка

См. [DEV_MODES.md](./DEV_MODES.md) — переключение local-api / production API.
