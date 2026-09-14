---
title: larena/filesystem
description: Логические файлы и безопасный жизненный цикл.
tags: [larena-filesystem, files, upload]
translation_key: larena.package.filesystem
---

# `larena/filesystem`

> **Для кого:** разработчик файловых функций. **Результат:** использовать
> логический файл через безопасный сервис. **Источник:** package README, Specs и
> integration evidence. **Статус:** guarded developer slice.

Filesystem владеет логическими файлами, их связью с хранилищем и безопасными
операциями жизненного цикла. Подтверждённая точка входа —
`Larena\Filesystem\Services\SafeFileService`.

Пакет зависит от Core и Access. Любая операция должна сохранить ограничение
пути, разрешение, связь с логическим объектом и возможность безопасного отказа.

## Проверка

В Root выполните:

```bash
php artisan larena:file-operation-guarded-flow-smoke
```

Ожидается `Status: PASS` для descriptor/session/storage flow, access/audit и
rollback/recovery boundaries.

## Ограничение

Полный браузерный upload/delete не принят для этой редакции. Guarded smoke не
равен готовому файловому менеджеру.
