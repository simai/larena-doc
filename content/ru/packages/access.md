---
title: larena/access
description: Операции, роли, области запросов и объяснимые решения доступа.
tags: [larena-access, permissions, roles]
translation_key: larena.package.access
---

# `larena/access`

> **Для кого:** разработчик защищённых операций. **Результат:** зарегистрировать
> и проверить право через поддерживаемый сервис. **Источник:** package README и
> developer docs. **Статус:** developer-testable Backend V1.

Access владеет зарегистрированными операциями, системными и пользовательскими
ролями, назначениями, query scopes и объяснением решения. Поддерживаемая граница:
`CustomRoleService`, `RoleAssignmentService` и `EffectiveAccessReader`.

Права доступа всегда относятся к зарегистрированной операции, субъекту и
области; одного названия роли для решения недостаточно.

## Подготовка и пример

После миграций синхронизируйте системные роли как install/update шаг:

```bash
php artisan larena:access-sync-system-presets
```

Объяснение решения для существующих субъектов:

```bash
php artisan larena:access-explain \
  user:admin_identity:1 user:admin_identity:2 \
  docara.page.write --json
```

## Ограничение

Не используйте внутренний `PersistentAccessStore` напрямую. Неизвестная
операция, устаревшая revision/incarnation или неверная область должны дать
отказ. Пакет не аутентифицирует пользователя и не строит admin UI.
