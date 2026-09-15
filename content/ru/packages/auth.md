---
title: larena/auth
description: Личности, вход, сессии, восстановление и MFA администратора.
tags: [larena-auth, login, user, mfa]
translation_key: larena.package.auth
---

# `larena/auth`

> **Для кого:** разработчик защищённых поверхностей. **Результат:** использовать
> личность и сессию без переноса прав в Auth. **Источник:** package README и
> developer docs. **Статус:** durable backend runtime, без production claim.

Auth владеет личностями администратора, парольными хешами, сессиями,
восстановлением, приглашением и TOTP MFA. Access отдельно решает, что этой
личности разрешено.

- Composer: `larena/auth`.
- Владелец: identity, session, recovery and MFA.
- Зрелость: durable backend runtime без production claim.

## Вход

Маршруты `/admin/login` и `/admin/logout` включаются явно через
`LARENA_AUTH_ADMIN_ENTRY_ROUTES=true`. Первый setup доступен при пустой таблице
личностей и может быть отдельно отключён через
`LARENA_AUTH_BROWSER_SETUP=false`.

## Зависимости и использование

Auth зависит от `larena/core` и Illuminate 13. Приложение получает уже
проверенную persistent session через package service provider. REST владеет
общими HTTP-гейтами, CSRF, rate limit и idempotency.

## Ограничение

Доставка recovery и invitation требует явно подключённого адаптера. Защита MFA
secret также должна быть связана приложением; встроенный fallback отказывает.

## Страница входа

Auth владеет смыслом входа, полями, ошибками, rate limit, MFA и сессией, но не
деревом страницы. `/admin/login` собирается из пакетных артефактов Layout через
Framework Recipe. CSRF, пароль, MFA proof, session ID и персонализированные
данные существуют только в текущем запросе и не попадают в общий snapshot.
